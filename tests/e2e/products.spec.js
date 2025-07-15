// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('Products Management', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the products page before each test
    await page.goto('/admin/products');
    // Verify we're on the products page
    await expect(page).toHaveTitle(/Products/);
  });

  test('should display products list', async ({ page }) => {
    // Check if the products table is visible
    await expect(page.locator('table')).toBeVisible();
    // Check if the table has headers
    await expect(page.locator('table th')).toBeVisible();
  });

  test('should create a new product', async ({ page }) => {
    // Click on the create new product button
    await page.getByRole('link', { name: /Add New/ }).click();
    
    // Verify we're on the create product form
    await expect(page).toHaveURL(/.*\/admin\/products\/create/);
    
    // Fill in the form
    const productName = `Test Product ${Date.now()}`;
    await page.getByLabel('Name').fill(productName);
    
    // Select a product category (assuming there's at least one)
    await page.locator('select[name="product_category_id"]').selectOption({ index: 1 });
    
    // Select a unit (assuming there's at least one)
    await page.locator('select[name="unit_id"]').selectOption({ index: 1 });
    
    // Fill in other required fields
    await page.getByLabel('Price').fill('100000');
    await page.getByLabel('Stock').fill('10');
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the products list
    await expect(page).toHaveURL(/.*\/admin\/products/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the new product appears in the table
    await expect(page.getByText(productName)).toBeVisible();
  });

  test('should edit an existing product', async ({ page }) => {
    // Click on the edit button for the first product
    await page.locator('table tbody tr').first().getByRole('link', { name: 'Edit' }).click();
    
    // Verify we're on the edit form
    await expect(page).toHaveURL(/.*\/admin\/products\/\d+\/edit/);
    
    // Update the product name
    const updatedName = `Updated Product ${Date.now()}`;
    await page.getByLabel('Name').fill(updatedName);
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the products list
    await expect(page).toHaveURL(/.*\/admin\/products/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the updated product appears in the table
    await expect(page.getByText(updatedName)).toBeVisible();
  });

  test('should delete a product', async ({ page }) => {
    // Get the name of the first product for verification later
    const productName = await page.locator('table tbody tr').first().locator('td').nth(1).textContent() || '';
    
    // Skip test if no product name found
    if (!productName) {
      test.skip();
      return;
    }
    
    // Click on the delete button for the first product
    await page.locator('table tbody tr').first().getByRole('button', { name: 'Delete' }).click();
    
    // Confirm deletion in the modal
    await page.getByRole('button', { name: 'Yes, delete it!' }).click();
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the product no longer appears in the table
    await expect(page.getByText(productName)).not.toBeVisible();
  });
});
