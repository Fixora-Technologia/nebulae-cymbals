// @ts-check
import { test, expect } from '@playwright/test';

// Use the authentication state from auth.setup.js
test.use({ storageState: 'playwright/.auth/user.json' });

test.describe('Reports', () => {
  test.beforeEach(async ({ page }) => {
    // Navigate to the reports page before each test
    await page.goto('/admin/reports');
    // Verify we're on the reports page
    await expect(page).toHaveTitle(/Reports/);
  });

  test('should display sales report options', async ({ page }) => {
    // Check if the sales report section is visible
    await expect(page.getByText('Sales Report')).toBeVisible();
    
    // Check if date range selectors are available
    await expect(page.getByLabel('Start Date')).toBeVisible();
    await expect(page.getByLabel('End Date')).toBeVisible();
    
    // Check if generate button is available
    await expect(page.getByRole('button', { name: 'Generate Report' })).toBeVisible();
  });

  test('should generate sales report for current month', async ({ page }) => {
    // Set date range to current month
    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
    
    const startDate = firstDay.toISOString().split('T')[0]; // Format as YYYY-MM-DD
    const endDate = lastDay.toISOString().split('T')[0]; // Format as YYYY-MM-DD
    
    await page.getByLabel('Start Date').fill(startDate);
    await page.getByLabel('End Date').fill(endDate);
    
    // Select report type
    await page.locator('select[name="report_type"]').selectOption('sales');
    
    // Click generate button
    await page.getByRole('button', { name: 'Generate Report' }).click();
    
    // Verify report is generated
    await expect(page.locator('.report-container')).toBeVisible();
    await expect(page.locator('.report-title')).toContainText('Sales Report');
    
    // Check if the report contains a table with data
    await expect(page.locator('.report-table')).toBeVisible();
  });

  test('should export sales report to Excel', async ({ page }) => {
    // Set date range
    await page.getByLabel('Start Date').fill('2025-01-01');
    await page.getByLabel('End Date').fill('2025-07-15');
    
    // Select report type
    await page.locator('select[name="report_type"]').selectOption('sales');
    
    // Generate report first
    await page.getByRole('button', { name: 'Generate Report' }).click();
    
    // Wait for report to be generated
    await expect(page.locator('.report-container')).toBeVisible();
    
    // Click export to Excel button
    const downloadPromise = page.waitForEvent('download');
    await page.getByRole('button', { name: 'Export to Excel' }).click();
    const download = await downloadPromise;
    
    // Verify the download started
    expect(download.suggestedFilename()).toContain('sales-report');
    expect(download.suggestedFilename()).toContain('.xlsx');
  });

  test('should export sales report to PDF', async ({ page }) => {
    // Set date range
    await page.getByLabel('Start Date').fill('2025-01-01');
    await page.getByLabel('End Date').fill('2025-07-15');
    
    // Select report type
    await page.locator('select[name="report_type"]').selectOption('sales');
    
    // Generate report first
    await page.getByRole('button', { name: 'Generate Report' }).click();
    
    // Wait for report to be generated
    await expect(page.locator('.report-container')).toBeVisible();
    
    // Click export to PDF button
    const downloadPromise = page.waitForEvent('download');
    await page.getByRole('button', { name: 'Export to PDF' }).click();
    const download = await downloadPromise;
    
    // Verify the download started
    expect(download.suggestedFilename()).toContain('sales-report');
    expect(download.suggestedFilename()).toContain('.pdf');
  });

  test('should generate inventory report', async ({ page }) => {
    // Select report type
    await page.locator('select[name="report_type"]').selectOption('inventory');
    
    // Click generate button
    await page.getByRole('button', { name: 'Generate Report' }).click();
    
    // Verify report is generated
    await expect(page.locator('.report-container')).toBeVisible();
    await expect(page.locator('.report-title')).toContainText('Inventory Report');
    
    // Check if the report contains a table with data
    await expect(page.locator('.report-table')).toBeVisible();
    
    // Check if the report shows stock levels
    await expect(page.locator('.report-table')).toContainText('Stock');
  });
});
