const puppeteer = require('puppeteer');

/**
 * Base scraper class for headless browser scraping
 */
class BaseScraper {
  constructor(options = {}) {
    this.browser = null;
    this.page = null;
    this.options = {
      headless: true,
      timeout: 30000,
      waitUntil: 'networkidle2',
      ...options
    };
  }

  async init() {
    this.browser = await puppeteer.launch({
      headless: this.options.headless,
      executablePath: '/home/Ben/.cache/puppeteer/chrome/linux-155.0.8059.39/chrome-linux64/chrome',
      args: [
        '--no-sandbox',
        '--disable-setuid-sandbox',
        '--disable-dev-shm-usage',
        '--disable-gpu',
        '--no-first-run',
        '--disable-gpu',
        '--disable-software-rasterizer',
        '--disable-extensions',
        '--disable-background-networking',
        '--disable-background-timer-throttling',
        '--disable-backgrounding-occluded-windows',
        '--disable-breakpad',
        '--disable-client-side-phishing-detection',
        '--disable-default-apps',
        '--disable-hang-monitor',
        '--disable-popup-blocking',
        '--disable-prompt-on-repost',
        '--disable-sync',
        '--disable-translate',
        '--metrics-recording-only',
        '--no-first-run',
        '--safebrowsing-disable-auto-update',
        '--enable-automation',
        '--password-store=basic',
        '--use-mock-keychain'
      ]
    });
    this.page = await this.browser.newPage();
    await this.page.setUserAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
    await this.page.setViewport({ width: 1280, height: 800 });
    this.page.setDefaultTimeout(this.options.timeout);
    this.page.setDefaultNavigationTimeout(this.options.timeout);
  }

  async close() {
    if (this.browser) {
      await this.browser.close();
    }
  }

  async scrape(url, selectors) {
    try {
      await this.page.goto(url, { waitUntil: this.options.waitUntil });
      await this.waitForSelectors(selectors);
      return await this.extractData(selectors);
    } catch (error) {
      console.error(`Scraping error for ${url}:`, error.message);
      throw error;
    }
  }

  async waitForSelectors(selectors) {
    for (const [key, selector] of Object.entries(selectors)) {
      try {
        await this.page.waitForSelector(selector, { timeout: 5000 });
      } catch (e) {
        console.warn(`Selector "${selector}" (${key}) not found, continuing...`);
      }
    }
  }

  async extractData(selectors) {
    return await this.page.evaluate((selectors) => {
      const results = {};
      
      for (const [key, selector] of Object.entries(selectors)) {
        const elements = document.querySelectorAll(selector);
        if (elements.length > 0) {
          if (elements.length === 1) {
            results[key] = elements[0].textContent?.trim() || elements[0].href || '';
          } else {
            results[key] = Array.from(elements).map(el => el.textContent?.trim() || el.href || '');
          }
        }
      }
      
      return results;
    }, selectors);
  }
}

module.exports = { BaseScraper };

/**
 * Tanitjobs scraper (RSS works, but we can also use headless for more detail)
 */
class TanitjobsScraper extends BaseScraper {
  async scrapeJobs(maxPages = 3) {
    const allJobs = [];
    const baseUrl = 'https://www.tanitjobs.com/rss';
    
    for (let page = 1; page <= maxPages; page++) {
      const url = page === 1 ? baseUrl : `${baseUrl}?page=${page}`;
      console.log(`Scraping page ${page}: ${url}`);
      
      try {
        await this.page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
        await this.page.waitForSelector('.job-card, .job-item, .offer-card, article, .job-listing', { timeout: 10000 }).catch(() => {});
        
        const jobs = await this.page.evaluate(() => {
          const jobs = [];
          const cards = document.querySelectorAll('.job-card, .job-item, .offer-card, article, .job-listing, .annonce-item');
          
          cards.forEach(card => {
            const titleEl = card.querySelector('h2, h3, .title, .job-title, .job-name, a[href*="/offre"], a[href*="/emploi"]');
            const companyEl = card.querySelector('.company, .employer, .entreprise, .societe, [class*="company"]');
            const locationEl = card.querySelector('.location, .lieu, .ville, .city, [class*="location"], [class*="lieu"]');
            const contractEl = card.querySelector('.contract, .type-contrat, .contrat, [class*="contract"]');
            const linkEl = card.querySelector('a[href*="/offre"], a[href*="/emploi"], a[href*="/annonce"]');
            const descEl = card.querySelector('.description, .summary, .excerpt, .content');
            
            if (titleEl) {
              jobs.push({
                title: titleEl.textContent?.trim(),
                company: companyEl?.textContent?.trim(),
                location: locationEl?.textContent?.trim(),
                contract_type: contractEl?.textContent?.trim(),
                url: linkEl?.href,
                description: descEl?.textContent?.trim()
              });
            }
          });
          
          return jobs;
        });
        
        if (jobs.length === 0) {
          console.log('No jobs found on page', page);
          break;
        }
        
        allJobs.push(...jobs);
        console.log(`Found ${jobs.length} jobs on page ${page}`);
        
        // Pause between pages
        await new Promise(r => setTimeout(r, 1000));
        
      } catch (error) {
        console.error(`Error on page ${page}:`, error.message);
        break;
      }
    }
    
    return allJobs;
  }
}

/**
 * Emploi.mg scraper
 */
class EmploiMgScraper extends BaseScraper {
  async scrapeJobs(maxPages = 3) {
    const allJobs = [];
    const baseUrl = 'https://www.emploi.mg/recherche';
    
    for (let page = 1; page <= maxPages; page++) {
      const url = page === 1 ? baseUrl : `${baseUrl}?page=${page}`;
      console.log(`Scraping page ${page}: ${url}`);
      
      try {
        await this.page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
        await this.page.waitForSelector('.job-card, .job-item, .offer-card, article, .job-listing', { timeout: 10000 }).catch(() => {});
        
        const jobs = await this.page.evaluate(() => {
          const jobs = [];
          const cards = document.querySelectorAll('.job-card, .job-item, .offer-card, article, .job-listing, .annonce-item');
          
          cards.forEach(card => {
            const titleEl = card.querySelector('h2, h3, .title, .job-title, .job-name, a[href*="/offre"], a[href*="/emploi"]');
            const companyEl = card.querySelector('.company, .employer, .entreprise, .societe, [class*="company"]');
            const locationEl = card.querySelector('.location, .lieu, .ville, .city, [class*="location"], [class*="lieu"]');
            const contractEl = card.querySelector('.contract, .type-contrat, .contrat, [class*="contract"]');
            const linkEl = card.querySelector('a[href*="/offre"], a[href*="/emploi"], a[href*="/annonce"]');
            const descEl = card.querySelector('.description, .summary, .excerpt, .content');
            
            if (titleEl) {
              jobs.push({
                title: titleEl.textContent?.trim(),
                company: companyEl?.textContent?.trim(),
                location: locationEl?.textContent?.trim(),
                contract_type: contractEl?.textContent?.trim(),
                url: linkEl?.href,
                description: descEl?.textContent?.trim()
              });
            }
          });
          
          return jobs;
        });
        
        if (jobs.length === 0) {
          console.log('No jobs found on page', page);
          break;
        }
        
        allJobs.push(...jobs);
        console.log(`Found ${jobs.length} jobs on page ${page}`);
        
        // Pause between pages
        await new Promise(r => setTimeout(r, 1000));
        
      } catch (error) {
        console.error(`Error on page ${page}:`, error.message);
        break;
      }
    }
    
    return allJobs;
  }
}

/**
 * PortalJob.mg scraper
 */
class PortalJobScraper extends BaseScraper {
  async scrapeJobs(maxPages = 3) {
    const allJobs = [];
    const baseUrl = 'https://portaljob.mg/offres-emploi';
    
    for (let page = 1; page <= maxPages; page++) {
      const url = page === 1 ? baseUrl : `${baseUrl}?page=${page}`;
      console.log(`Scraping page ${page}: ${url}`);
      
      try {
        await this.page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
        await this.page.waitForSelector('.job-card, .job-item, .offer-card, article, .job-listing', { timeout: 10000 }).catch(() => {});
        
        const jobs = await this.page.evaluate(() => {
          const jobs = [];
          const cards = document.querySelectorAll('.job-card, .job-item, .offer-card, article, .job-listing, .annonce-item');
          
          cards.forEach(card => {
            const titleEl = card.querySelector('h2, h3, .title, .job-title, .job-name, a[href*="/offre"], a[href*="/emploi"]');
            const companyEl = card.querySelector('.company, .employer, .entreprise, .societe, [class*="company"]');
            const locationEl = card.querySelector('.location, .lieu, .ville, .city, [class*="location"], [class*="lieu"]');
            const contractEl = card.querySelector('.contract, .type-contrat, .contrat, [class*="contract"]');
            const linkEl = card.querySelector('a[href*="/offre"], a[href*="/emploi"], a[href*="/annonce"]');
            const descEl = card.querySelector('.description, .summary, .excerpt, .content');
            
            if (titleEl) {
              jobs.push({
                title: titleEl.textContent?.trim(),
                company: companyEl?.textContent?.trim(),
                location: locationEl?.textContent?.trim(),
                contract_type: contractEl?.textContent?.trim(),
                url: linkEl?.href,
                description: descEl?.textContent?.trim()
              });
            }
          });
          
          return jobs;
        });
        
        if (jobs.length === 0) {
          console.log('No jobs found on page', page);
          break;
        }
        
        allJobs.push(...jobs);
        console.log(`Found ${jobs.length} jobs on page ${page}`);
        
        await new Promise(r => setTimeout(r, 1000));
        
      } catch (error) {
        console.error(`Error on page ${page}:`, error.message);
        break;
      }
    }
    
    return allJobs;
  }
}

module.exports = { BaseScraper, EmploiMgScraper, TanitjobsScraper, PortalJobScraper };