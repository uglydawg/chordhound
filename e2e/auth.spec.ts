import { test, expect } from '@playwright/test';

test.describe('Authentication', () => {
  test('can login with email and password', async ({ page }) => {
    await page.goto('/login');
    
    await page.fill('input[type="email"]', 'test@example.com');
    await page.fill('input[type="password"]', 'password');
    await page.click('button:has-text("Log in")');
    
    // Should redirect to dashboard
    await expect(page).toHaveURL('/dashboard');
  });

  test('can access Google OAuth', async ({ page }) => {
    await page.goto('/login');
    
    const googleButton = page.locator('a:has-text("Continue with Google")');
    await expect(googleButton).toBeVisible();
    
    // Check that clicking redirects to Google OAuth
    const href = await googleButton.getAttribute('href');
    expect(href).toContain('/auth/google');
  });

  test('can open magic link modal', async ({ page }) => {
    await page.goto('/login');
    
    await page.click('button:has-text("Continue with Magic Link")');
    
    // Modal should open
    const modal = page.locator('[data-flux-modal="magic-link"]');
    await expect(modal).toBeVisible();
    
    // Should see email input
    const emailInput = modal.locator('input[type="email"]');
    await expect(emailInput).toBeVisible();
  });

  test('can open auth code modal', async ({ page }) => {
    await page.goto('/login');
    
    await page.click('button:has-text("Continue with Auth Code")');
    
    // Modal should open
    const modal = page.locator('[data-flux-modal="auth-code"]');
    await expect(modal).toBeVisible();
    
    // Should see email input
    const emailInput = modal.locator('input[type="email"]');
    await expect(emailInput).toBeVisible();
  });
});