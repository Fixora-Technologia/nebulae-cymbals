// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('Dashboard', () => {
  test('should display dashboard with key metrics', async ({ page }) => {
    // Navigate to the dashboard
    await page.goto('/admin/dashboard');
    
    // Verify we're on the dashboard page
    await expect(page).toHaveTitle(/Dashboard/);
    
    // Check if key metrics are displayed
    await expect(page.locator('.small-box')).toBeVisible();
    
    // Check if charts are rendered
    await expect(page.locator('.chart-container')).toBeVisible();
  });

  test('should display monthly sales chart', async ({ page }) => {
    // Navigate to the dashboard
    await page.goto('/admin/dashboard');
    
    // Check if the monthly sales chart is visible
    await expect(page.locator('#monthlySalesChart')).toBeVisible();
    
    // Verify chart controls are working
    await page.locator('select[name="chart_year"]').selectOption('2024');
    
    // Wait for chart to update after selection
    await page.waitForTimeout(1000);
    
    // Verify chart is still visible after selection change
    await expect(page.locator('#monthlySalesChart')).toBeVisible();
  });

  test('should display product inventory status', async ({ page }) => {
    // Navigate to the dashboard
    await page.goto('/admin/dashboard');
    
    // Check if the inventory status section is visible
    await expect(page.locator('.inventory-status')).toBeVisible();
    
    // Check if low stock warnings are displayed if any
    const lowStockWarnings = await page.locator('.low-stock-warning').count();
    console.log(`Found ${lowStockWarnings} low stock warnings`);
  });

  test('should navigate to modules from dashboard', async ({ page }) => {
    // Navigate to the dashboard
    await page.goto('/admin/dashboard');
    
    // Click on the Products quick link
    await page.getByRole('link', { name: /Products/i }).first().click();
    
    // Verify navigation to products page
    await expect(page).toHaveURL(/.*\/admin\/products/);
    
    // Navigate back to dashboard
    await page.goto('/admin/dashboard');
    
    // Click on the Transactions quick link
    await page.getByRole('link', { name: /Transactions/i }).first().click();
    
    // Verify navigation to transactions page
    await expect(page).toHaveURL(/.*\/admin\/transactions/);
  });
});
