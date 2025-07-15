// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('Units Management', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the units page before each test
    await page.goto('/admin/units');
    // Verify we're on the units page
    await expect(page).toHaveTitle(/Units/);
  });

  test('should display units list', async ({ page }) => {
    // Check if the units table is visible
    await expect(page.locator('table')).toBeVisible();
    // Check if the table has headers
    await expect(page.locator('table th')).toHaveCount(3); // Adjust based on your actual columns
  });

  test('should create a new unit', async ({ page }) => {
    // Click on the create new unit button
    await page.getByRole('link', { name: /Add New/ }).click();
    
    // Verify we're on the create unit form
    await expect(page).toHaveURL(/.*\/admin\/units\/create/);
    
    // Fill in the form
    const unitName = `Test Unit ${Date.now()}`;
    await page.getByLabel('Name').fill(unitName);
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the units list
    await expect(page).toHaveURL(/.*\/admin\/units/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the new unit appears in the table
    await expect(page.getByText(unitName)).toBeVisible();
  });

  test('should edit an existing unit', async ({ page }) => {
    // Click on the edit button for the first unit
    await page.locator('table tbody tr').first().getByRole('link', { name: 'Edit' }).click();
    
    // Verify we're on the edit form
    await expect(page).toHaveURL(/.*\/admin\/units\/\d+\/edit/);
    
    // Update the unit name
    const updatedName = `Updated Unit ${Date.now()}`;
    await page.getByLabel('Name').fill(updatedName);
    
    // Submit the form
    await page.getByRole('button', { name: 'Save' }).click();
    
    // Verify we're redirected back to the units list
    await expect(page).toHaveURL(/.*\/admin\/units/);
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the updated unit appears in the table
    await expect(page.getByText(updatedName)).toBeVisible();
  });

  test('should delete a unit', async ({ page }) => {
    // Get the name of the first unit for verification later
    const unitName = await page.locator('table tbody tr').first().locator('td').nth(1).textContent() || '';
    
    // Skip test if no unit name found
    if (!unitName) {
      test.skip();
      return;
    }
    
    // Click on the delete button for the first unit
    await page.locator('table tbody tr').first().getByRole('button', { name: 'Delete' }).click();
    
    // Confirm deletion in the modal
    await page.getByRole('button', { name: 'Yes, delete it!' }).click();
    
    // Verify success message is displayed
    await expect(page.locator('.alert-success')).toBeVisible();
    
    // Verify the unit no longer appears in the table
    await expect(page.getByText(unitName)).not.toBeVisible();
  });
});
