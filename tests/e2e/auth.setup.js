// @ts-check
import { test as setup, expect } from "@playwright/test";

const authFile = "playwright/.auth/user.json";

setup("authenticate", async ({ page }) => {
    // Navigate to the login page
    await page.goto("/login");

    // Fill in login credentials - update these with valid credentials for your app
    await page.getByLabel("Email").fill("admin@gmail.com");
    await page.getByLabel("Password").fill("12345678");

    // Click the login button and wait for navigation
    await page.getByRole("button", { name: "Login" }).click();

    // Verify we're logged in by checking for dashboard element
    await expect(page.locator(".user-panel")).toBeVisible();

    // Save the authentication state to reuse in tests
    await page.context().storageState({ path: authFile });
});
