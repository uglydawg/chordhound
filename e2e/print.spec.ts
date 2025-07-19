import { test, expect } from '@playwright/test';

test.describe('Print Functionality', () => {
  test('print button triggers print dialog', async ({ page }) => {
    await page.goto('/');
    
    // Listen for print dialog
    let printDialogOpened = false;
    page.on('dialog', dialog => {
      if (dialog.type() === 'print') {
        printDialogOpened = true;
        dialog.dismiss();
      }
    });
    
    // Alternatively, check window.print was called
    await page.evaluate(() => {
      window.print = () => {
        window.printCalled = true;
      };
    });
    
    // Click print button
    await page.click('button:has-text("Print Chord Sheet")');
    
    // Check print was called
    const printCalled = await page.evaluate(() => window.printCalled);
    expect(printCalled).toBe(true);
  });

  test('print styles hide navigation', async ({ page }) => {
    await page.goto('/');
    
    // Emulate print media
    await page.emulateMedia({ media: 'print' });
    
    // Navigation should be hidden
    const sidebar = page.locator('[data-flux-sidebar]');
    await expect(sidebar).toBeHidden();
    
    // Print button should be hidden
    const printButton = page.locator('button:has-text("Print Chord Sheet")');
    await expect(printButton).toBeHidden();
    
    // Piano should still be visible
    const piano = page.locator('svg').first();
    await expect(piano).toBeVisible();
  });
});