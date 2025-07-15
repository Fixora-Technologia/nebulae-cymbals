// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('User Management', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the users page before each test
    await page.goto('/admin/users');
    // Verify we're on the users page
    await expect(page).toHaveTitle(/Users/);
  });

  test('should display users list', async ({ page }) => {
    // Check if the users table is visible
    await expect(page.locator('table')).toBeVisible();
    // Check if the table has headers
    await expect(page.locator('table th')).toBeVisible();
  });

  test('should create a new user', async ({ page }) => {
    // Click on the create new user button
    await page.getByRole('link', { name: /Add New/ }).click();
    
    // Verify we're on the create user form
    await expect(page).toHaveURL(/.*\/admin\/users\/create/);
    
    // Fill in the form
    const userName = `Test User ${Date.now()}`;
    const userEmail = `test${Date.now()}@example.com`;
    
    await page.getByLabel('Name').fill(userName);
    await page.getByLabel('Email').fill(userEmail);
    await page.getByLabel('Password').fill('password123');
    await page.getByLabel('Confirm Password').fill('password123');
    
    // Select a role (assuming Admin Gudang role exists)
    await page.locator('input[type="checkbox"][value="Admin Gudang"]').check();
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the users list
    await expect(page).toHaveURL(/.*\/admin\/users/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the new user appears in the table
    await expect(page.getByText(userName)).toBeVisible();
    await expect(page.getByText(userEmail)).toBeVisible();
  });

  test('should edit an existing user', async ({ page }) => {
    // Click on the edit button for the first non-admin user
    // We'll skip the first row (which might be the admin) and edit the second user
    await page.locator('table tbody tr').nth(1).getByRole('link', { name: 'Edit' }).click();
    
    // Verify we're on the edit form
    await expect(page).toHaveURL(/.*\/admin\/users\/\d+\/edit/);
    
    // Update the user name
    const updatedName = `Updated User ${Date.now()}`;
    await page.getByLabel('Name').fill(updatedName);
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the users list
    await expect(page).toHaveURL(/.*\/admin\/users/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the updated user appears in the table
    await expect(page.getByText(updatedName)).toBeVisible();
  });

  test('should not be able to delete the logged-in user', async ({ page }) => {
    // Get the email of the logged-in user (assuming it's displayed in the user panel)
    await page.locator('.user-panel').click();
    const loggedInUserEmail = await page.locator('.user-panel .d-block').textContent() || '';
    
    // Find the row with the logged-in user
    const userRow = page.locator(`table tbody tr:has-text("${loggedInUserEmail}")`);
    
    // Verify that the delete button is disabled or not present for the logged-in user
    const deleteButton = userRow.getByRole('button', { name: 'Delete' });
    const isDeleteButtonPresent = await deleteButton.count() > 0;
    
    if (isDeleteButtonPresent) {
      // If the button exists, it should be disabled
      await expect(deleteButton).toBeDisabled();
    } else {
      // If the button doesn't exist, that's also acceptable
      console.log('Delete button not present for logged-in user (expected behavior)');
    }
  });

  test('should display user roles correctly', async ({ page }) => {
    // Check if the roles column is visible in the table
    await expect(page.locator('table th:has-text("Roles")')).toBeVisible();
    
    // Check if at least one user has the "Super Admin" role
    await expect(page.locator('table tbody').getByText('Super Admin')).toBeVisible();
    
    // Check if the other roles from the seeder are present
    await expect(page.locator('table tbody').getByText('Kepala Gudang', { exact: false })).toBeVisible();
    await expect(page.locator('table tbody').getByText('Admin Gudang', { exact: false })).toBeVisible();
  });
});
