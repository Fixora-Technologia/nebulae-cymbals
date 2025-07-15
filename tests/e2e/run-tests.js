#!/usr/bin/env node

/**
 * Custom test runner for Playwright tests
 * Makes it easier to run specific tests or groups of tests
 */

import { execSync } from 'child_process';
import readline from 'readline';
import fs from 'fs';
import path from 'path';

const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

// Get all test files
const testDir = path.join(process.cwd(), 'tests', 'e2e');
const testFiles = fs.readdirSync(testDir)
  .filter(file => file.endsWith('.spec.js'))
  .map(file => ({
    name: file.replace('.spec.js', ''),
    path: path.join('tests', 'e2e', file)
  }));

// Define test groups
const testGroups = {
  'auth': ['auth.spec.js'],
  'data-management': ['units.spec.js', 'product-categories.spec.js', 'products.spec.js'],
  'transactions': ['transactions.spec.js'],
  'dashboard': ['dashboard.spec.js'],
  'all': testFiles.map(f => f.path)
};

// Display menu
console.log('🎭 Nebulae Cymbals Playwright Test Runner 🎭\n');
console.log('Available options:');
console.log('1. Run all tests');
console.log('2. Run authentication tests');
console.log('3. Run data management tests (Units, Categories, Products)');
console.log('4. Run transaction tests');
console.log('5. Run dashboard tests');
console.log('6. Run a specific test file');
console.log('7. Run tests with UI mode');
console.log('8. View test report');
console.log('9. Exit\n');

rl.question('Select an option (1-9): ', (answer) => {
  switch (answer) {
    case '1':
      runTests(testGroups.all);
      break;
    case '2':
      runTests(testGroups.auth);
      break;
    case '3':
      runTests(testGroups['data-management']);
      break;
    case '4':
      runTests(testGroups.transactions);
      break;
    case '5':
      runTests(testGroups.dashboard);
      break;
    case '6':
      selectTestFile();
      break;
    case '7':
      runTestsWithUI();
      break;
    case '8':
      showReport();
      break;
    case '9':
      console.log('Exiting...');
      rl.close();
      break;
    default:
      console.log('Invalid option. Exiting...');
      rl.close();
  }
});

function selectTestFile() {
  console.log('\nAvailable test files:');
  testFiles.forEach((file, index) => {
    console.log(`${index + 1}. ${file.name}`);
  });
  
  rl.question(`\nSelect a test file (1-${testFiles.length}): `, (answer) => {
    const index = parseInt(answer) - 1;
    if (index >= 0 && index < testFiles.length) {
      runTests([testFiles[index].path]);
    } else {
      console.log('Invalid selection. Exiting...');
      rl.close();
    }
  });
}

function runTests(tests) {
  console.log(`\nRunning tests: ${tests.join(', ')}`);
  try {
    execSync(`npx playwright test ${tests.join(' ')}`, { stdio: 'inherit' });
  } catch (error) {
    console.error('Tests failed with errors');
  }
  rl.close();
}

function runTestsWithUI() {
  console.log('\nRunning tests with UI mode');
  try {
    execSync('npx playwright test --ui', { stdio: 'inherit' });
  } catch (error) {
    console.error('Tests failed with errors');
  }
  rl.close();
}

function showReport() {
  console.log('\nOpening test report');
  try {
    execSync('npx playwright show-report', { stdio: 'inherit' });
  } catch (error) {
    console.error('Failed to open report');
  }
  rl.close();
}
