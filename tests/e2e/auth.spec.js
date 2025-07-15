// @ts-check
import { test, expect } from "@playwright/test";

test.describe("Authentication", () => {
    test("should display login page", async ({ page }) => {
        // Navigate to the login page
        await page.goto("/login");

        // Verify we're on the login page
        await expect(
            page.getByRole("heading", { name: "Login" })
        ).toBeVisible();
        await expect(page.getByLabel("Email")).toBeVisible();
        await expect(page.getByLabel("Password")).toBeVisible();
        await expect(page.getByRole("button", { name: "Login" })).toBeVisible();
    });

    test("should show error with invalid credentials", async ({ page }) => {
        // Navigate to the login page
        await page.goto("/login");

        // Fill in invalid credentials
        await page.getByLabel("Email").fill("invalid@example.com");
        await page.getByLabel("Password").fill("wrongpassword");

        // Submit the form
        await page.getByRole("button", { name: "Login" }).click();

        // Verify error message is displayed
        await expect(page.locator(".invalid-feedback")).toBeVisible();
    });

    test("should login with valid credentials", async ({ page }) => {
        // Navigate to the login page
        await page.goto("/login");

        // Fill in valid credentials - update these with valid credentials for your app
        await page.getByLabel("Email").fill("admin@example.com");
        await page.getByLabel("Password").fill("password");

        // Submit the form
        await page.getByRole("button", { name: "Login" }).click();

        // Verify we're redirected to the dashboard
        await expect(page).toHaveURL(/.*\/admin\/dashboard/);

        // Verify user panel is visible indicating we're logged in
        await expect(page.locator(".user-panel")).toBeVisible();
    });

    test("should logout successfully", async ({ page }) => {
        // First login
        await page.goto("/login");
        await page.getByLabel("Email").fill("admin@example.com");
        await page.getByLabel("Password").fill("password");
        await page.getByRole("button", { name: "Login" }).click();

        // Verify we're logged in
        await expect(page.locator(".user-panel")).toBeVisible();

        // Click on the user menu to expand it
        await page.locator(".user-panel").click();

        // Click on the logout button
        await page.getByRole("link", { name: "Logout" }).click();

        // Verify we're redirected to the login page
        await expect(page).toHaveURL(/.*\/login/);

        // Verify login form is visible
        await expect(page.getByRole("button", { name: "Login" })).toBeVisible();
    });
});
