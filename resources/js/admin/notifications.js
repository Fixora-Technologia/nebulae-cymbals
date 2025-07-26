/**
 * Notifications handler for Nebulae Cymbals admin dashboard
 */

// Function to fetch unread notifications
function fetchUnreadNotifications() {
    fetch('/mindo/notifications/unread')
        .then(response => response.json())
        .then(data => {
            updateNotificationBadge(data.count);
            updateNotificationDropdown(data.notifications);
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
            document.querySelector('.notification-loading').style.display = 'none';
            document.querySelector('.notification-empty').style.display = 'block';
        });
}

// Update notification badge count
function updateNotificationBadge(count) {
    const badge = document.querySelector('.notification-count');
    
    if (count > 0) {
        badge.textContent = count;
        badge.style.display = 'inline-block';
    } else {
        badge.style.display = 'none';
    }
}

// Update notification dropdown content
function updateNotificationDropdown(notifications) {
    const container = document.querySelector('.notification-items');
    const loadingElement = document.querySelector('.notification-loading');
    const emptyElement = document.querySelector('.notification-empty');
    
    // Hide loading spinner
    loadingElement.style.display = 'none';
    
    if (notifications.length === 0) {
        // Show empty message if no notifications
        emptyElement.style.display = 'block';
        return;
    }
    
    // Hide empty message
    emptyElement.style.display = 'none';
    
    // Clear existing notification items (except loading and empty elements)
    const existingItems = container.querySelectorAll('.dropdown-item:not(.notification-loading):not(.notification-empty)');
    existingItems.forEach(item => item.remove());
    
    // Add notification items
    notifications.forEach(notification => {
        const notificationItem = createNotificationItem(notification);
        container.appendChild(notificationItem);
        
        // Add divider after each item
        const divider = document.createElement('div');
        divider.className = 'dropdown-divider';
        container.appendChild(divider);
    });
}

// Create a notification item element
function createNotificationItem(notification) {
    const item = document.createElement('a');
    item.href = '#';
    item.className = 'dropdown-item';
    item.dataset.id = notification.id;
    
    // Add click event to mark as read
    item.addEventListener('click', function(e) {
        e.preventDefault();
        markNotificationAsRead(notification.id);
    });
    
    // Format the time
    const createdAt = new Date(notification.created_at);
    const timeAgo = formatTimeAgo(createdAt);
    
    // Check notification type and create appropriate content
    if (notification.type.includes('LowStockNotification')) {
        const productName = notification.data.product_name;
        const currentStock = notification.data.current_stock;
        const minStock = notification.data.min_stock;
        
        item.innerHTML = `
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
            <span class="text-truncate d-inline-block" style="max-width: 200px;">
                <strong>${productName}</strong> is low on stock
            </span>
            <span class="float-end text-muted text-sm">${timeAgo}</span>
            <br>
            <small class="text-muted">Stock: ${currentStock} / Min: ${minStock}</small>
        `;
    } else {
        // Generic notification format
        const message = notification.data.message || 'System notification';
        
        item.innerHTML = `
            <i class="bi bi-info-circle-fill text-info me-2"></i>
            <span>${message}</span>
            <span class="float-end text-muted text-sm">${timeAgo}</span>
        `;
    }
    
    return item;
}

// Format time ago (e.g., "2 hours ago")
function formatTimeAgo(date) {
    const now = new Date();
    const diffInSeconds = Math.floor((now - date) / 1000);
    
    if (diffInSeconds < 60) {
        return 'just now';
    }
    
    const diffInMinutes = Math.floor(diffInSeconds / 60);
    if (diffInMinutes < 60) {
        return `${diffInMinutes} min${diffInMinutes > 1 ? 's' : ''} ago`;
    }
    
    const diffInHours = Math.floor(diffInMinutes / 60);
    if (diffInHours < 24) {
        return `${diffInHours} hour${diffInHours > 1 ? 's' : ''} ago`;
    }
    
    const diffInDays = Math.floor(diffInHours / 24);
    if (diffInDays < 30) {
        return `${diffInDays} day${diffInDays > 1 ? 's' : ''} ago`;
    }
    
    const diffInMonths = Math.floor(diffInDays / 30);
    return `${diffInMonths} month${diffInMonths > 1 ? 's' : ''} ago`;
}

// Initialize notifications
document.addEventListener('DOMContentLoaded', function() {
    // Fetch notifications when the page loads
    fetchUnreadNotifications();
    
    // Set up periodic refresh (every 60 seconds)
    setInterval(fetchUnreadNotifications, 60000);
    
    // Refresh notifications when dropdown is opened
    const notificationDropdown = document.getElementById('notification-dropdown');
    if (notificationDropdown) {
        notificationDropdown.addEventListener('show.bs.dropdown', fetchUnreadNotifications);
    }
});

// Function to mark a notification as read
function markNotificationAsRead(notificationId) {
    // Get the CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Send POST request to mark notification as read
    fetch(`/mindo/notifications/${notificationId}/read`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(response => {
        if (response.ok) {
            // Refresh notifications after marking as read
            fetchUnreadNotifications();
            
            // If the notification has a specific URL to redirect to, handle it here
            // For now, we'll just refresh the notifications
        } else {
            console.error('Error marking notification as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

export { fetchUnreadNotifications };
