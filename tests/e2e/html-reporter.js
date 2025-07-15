// @ts-check
import fs from 'fs';
import path from 'path';

/**
 * Custom HTML reporter for Playwright tests
 * Creates a beautiful HTML report with test results
 */
class HTMLReporter {
  constructor(options = {}) {
    this.outputFile = options.outputFile || 'playwright-report/custom-report.html';
    this.title = options.title || 'Nebulae Cymbals E2E Test Report';
    this.startTime = new Date();
    this.results = {
      passed: 0,
      failed: 0,
      skipped: 0,
      total: 0,
      duration: 0,
      tests: []
    };
  }

  onBegin(config, suite) {
    this.startTime = new Date();
    this.results.total = suite.allTests().length;
    console.log(`Starting tests: ${this.results.total} tests to run`);
  }

  onTestBegin(test) {
    console.log(`Starting test: ${test.title}`);
  }

  onTestEnd(test, result) {
    const status = result.status;
    if (status === 'passed' || status === 'failed' || status === 'skipped') {
      this.results[status]++;
    }
    
    // @ts-ignore - TypeScript doesn't know about the structure of our tests array
    this.results.tests.push({
      title: test.title,
      file: path.relative(process.cwd(), test.location.file),
      status,
      duration: result.duration,
      error: result.error ? result.error.message : null,
      retry: test.retry
    });
    
    console.log(`Finished test: ${test.title} (${status})`);
  }

  onEnd(result) {
    const endTime = new Date();
    // @ts-ignore - TypeScript doesn't understand Date subtraction
    this.results.duration = endTime.getTime() - this.startTime.getTime();
    this._generateReport();
    console.log(`Tests finished. Report generated at ${this.outputFile}`);
    return result;
  }

  _generateReport() {
    const reportDir = path.dirname(this.outputFile);
    if (!fs.existsSync(reportDir)) {
      fs.mkdirSync(reportDir, { recursive: true });
    }

    const html = this._generateHTML();
    fs.writeFileSync(this.outputFile, html);
  }

  _generateHTML() {
    const passRate = this.results.total > 0 
      ? Math.round((this.results.passed / this.results.total) * 100) 
      : 0;
    
    // @ts-ignore - TypeScript doesn't know about the structure of our tests array
    const testResults = this.results.tests.map(test => `
      <div class="test-result ${test.status}">
        <div class="test-header">
          <span class="test-title">${test.title}</span>
          <span class="test-status ${test.status}">${test.status}</span>
          <span class="test-duration">${(test.duration / 1000).toFixed(2)}s</span>
        </div>
        <div class="test-details">
          <div class="test-file">File: ${test.file}</div>
          ${test.error ? `<div class="test-error">Error: ${test.error}</div>` : ''}
        </div>
      </div>
    `).join('');

    return `
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>${this.title}</title>
      <style>
        :root {
          --color-primary: #3498db;
          --color-success: #2ecc71;
          --color-warning: #f39c12;
          --color-danger: #e74c3c;
          --color-light: #f8f9fa;
          --color-dark: #343a40;
        }
        
        body {
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          line-height: 1.6;
          color: #333;
          margin: 0;
          padding: 0;
          background-color: #f5f5f5;
        }
        
        .container {
          max-width: 1200px;
          margin: 0 auto;
          padding: 20px;
        }
        
        header {
          background-color: var(--color-primary);
          color: white;
          padding: 20px;
          border-radius: 5px 5px 0 0;
          margin-bottom: 20px;
        }
        
        h1 {
          margin: 0;
          font-size: 24px;
        }
        
        .summary {
          display: flex;
          justify-content: space-between;
          background-color: white;
          padding: 20px;
          border-radius: 5px;
          box-shadow: 0 2px 5px rgba(0,0,0,0.1);
          margin-bottom: 20px;
        }
        
        .summary-item {
          text-align: center;
          padding: 10px;
          border-radius: 5px;
        }
        
        .summary-item.total {
          background-color: var(--color-light);
        }
        
        .summary-item.passed {
          background-color: var(--color-success);
          color: white;
        }
        
        .summary-item.failed {
          background-color: var(--color-danger);
          color: white;
        }
        
        .summary-item.skipped {
          background-color: var(--color-warning);
          color: white;
        }
        
        .summary-number {
          font-size: 24px;
          font-weight: bold;
        }
        
        .summary-label {
          font-size: 14px;
          text-transform: uppercase;
        }
        
        .progress-bar {
          height: 10px;
          background-color: #e9ecef;
          border-radius: 5px;
          margin-bottom: 20px;
          overflow: hidden;
        }
        
        .progress {
          height: 100%;
          background-color: var(--color-success);
          width: ${passRate}%;
        }
        
        .test-results {
          background-color: white;
          border-radius: 5px;
          box-shadow: 0 2px 5px rgba(0,0,0,0.1);
          overflow: hidden;
        }
        
        .test-result {
          padding: 15px;
          border-bottom: 1px solid #eee;
        }
        
        .test-result:last-child {
          border-bottom: none;
        }
        
        .test-result.passed {
          border-left: 5px solid var(--color-success);
        }
        
        .test-result.failed {
          border-left: 5px solid var(--color-danger);
        }
        
        .test-result.skipped {
          border-left: 5px solid var(--color-warning);
        }
        
        .test-header {
          display: flex;
          justify-content: space-between;
          align-items: center;
          margin-bottom: 10px;
        }
        
        .test-title {
          font-weight: bold;
          flex-grow: 1;
        }
        
        .test-status {
          padding: 3px 8px;
          border-radius: 3px;
          font-size: 12px;
          text-transform: uppercase;
          font-weight: bold;
        }
        
        .test-status.passed {
          background-color: var(--color-success);
          color: white;
        }
        
        .test-status.failed {
          background-color: var(--color-danger);
          color: white;
        }
        
        .test-status.skipped {
          background-color: var(--color-warning);
          color: white;
        }
        
        .test-duration {
          margin-left: 10px;
          color: #6c757d;
          font-size: 14px;
        }
        
        .test-details {
          font-size: 14px;
          color: #6c757d;
        }
        
        .test-error {
          margin-top: 10px;
          padding: 10px;
          background-color: #fff3f3;
          border-left: 3px solid var(--color-danger);
          color: #333;
          font-family: monospace;
          white-space: pre-wrap;
        }
        
        footer {
          margin-top: 20px;
          text-align: center;
          color: #6c757d;
          font-size: 14px;
        }
      </style>
    </head>
    <body>
      <div class="container">
        <header>
          <h1>${this.title}</h1>
          <div>Generated on ${new Date().toLocaleString()}</div>
        </header>
        
        <div class="summary">
          <div class="summary-item total">
            <div class="summary-number">${this.results.total}</div>
            <div class="summary-label">Total</div>
          </div>
          <div class="summary-item passed">
            <div class="summary-number">${this.results.passed}</div>
            <div class="summary-label">Passed</div>
          </div>
          <div class="summary-item failed">
            <div class="summary-number">${this.results.failed}</div>
            <div class="summary-label">Failed</div>
          </div>
          <div class="summary-item skipped">
            <div class="summary-number">${this.results.skipped}</div>
            <div class="summary-label">Skipped</div>
          </div>
          <div class="summary-item total">
            <div class="summary-number">${(this.results.duration / 1000).toFixed(2)}s</div>
            <div class="summary-label">Duration</div>
          </div>
        </div>
        
        <div class="progress-bar">
          <div class="progress"></div>
        </div>
        
        <div class="test-results">
          ${testResults}
        </div>
        
        <footer>
          <p>Nebulae Cymbals Factory - Playwright Test Report</p>
        </footer>
      </div>
    </body>
    </html>
    `;
  }
}

export default HTMLReporter;
