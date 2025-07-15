// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('Product Categories Management', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the product categories page before each test
    await page.goto('/admin/product-categories');
    // Verify we're on the product categories page
    await expect(page).toHaveTitle(/Product Categories/);
  });

  test('should display product categories list', async ({ page }) => {
    // Check if the product categories table is visible
    await expect(page.locator('table')).toBeVisible();
    // Check if the table has headers
    await expect(page.locator('table th')).toBeVisible();
  });

  test('should create a new product category', async ({ page }) => {
    // Click on the create new product category button
    await page.getByRole('link', { name: /Add New/ }).click();
    
    // Verify we're on the create product category form
    await expect(page).toHaveURL(/.*\/admin\/product-categories\/create/);
    
    // Fill in the form
    const categoryName = `Test Category ${Date.now()}`;
    await page.getByLabel('Name').fill(categoryName);
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the product categories list
    await expect(page).toHaveURL(/.*\/admin\/product-categories/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the new product category appears in the table
    await expect(page.getByText(categoryName)).toBeVisible();
  });

  test('should edit an existing product category', async ({ page }) => {
    // Click on the edit button for the first product category
    await page.locator('table tbody tr').first().getByRole('link', { name: 'Edit' }).click();
    
    // Verify we're on the edit form
    await expect(page).toHaveURL(/.*\/admin\/product-categories\/\d+\/edit/);
    
    // Update the product category name
    const updatedName = `Updated Category ${Date.now()}`;
    await page.getByLabel('Name').fill(updatedName);
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the product categories list
    await expect(page).toHaveURL(/.*\/admin\/product-categories/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the updated product category appears in the table
    await expect(page.getByText(updatedName)).toBeVisible();
  });

  test('should delete a product category', async ({ page }) => {
    // Get the name of the first product category for verification later
    const categoryName = await page.locator('table tbody tr').first().locator('td').nth(1).textContent() || '';
    
    // Skip test if no category name found
    if (!categoryName) {
      test.skip();
      return;
    }
    
    // Click on the delete button for the first product category
    await page.locator('table tbody tr').first().getByRole('button', { name: 'Delete' }).click();
    
    // Confirm deletion in the modal
    await page.getByRole('button', { name: 'Yes, delete it!' }).click();
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the product category no longer appears in the table
    await expect(page.getByText(categoryName)).not.toBeVisible();
  });
});
