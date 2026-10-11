#!/usr/bin/env node

const { EmploiMgScraper, TanitjobsScraper, PortalJobScraper } = require('./scrapers.js');

async function main() {
  const source = process.argv[2] || 'tanitjobs';
  const maxPages = parseInt(process.argv[3]) || 2;
  
  let scraper;
  switch (source) {
    case 'tanitjobs':
      scraper = new (require('./scrapers.js').TanitjobsScraper)({ headless: true });
      break;
    case 'emploi_mg':
      scraper = new (require('./scrapers.js').EmploiMgScraper)({ headless: true });
      break;
    case 'portaljob_mg':
      scraper = new (require('./scrapers.js').PortalJobScraper)({ headless: true });
      break;
    default:
      console.error('Unknown source:', source);
      process.exit(1);
  }
  
  try {
    await scraper.init();
    console.log(`Starting scraper for ${source}...`);
    
    let jobs = [];
    if (source === 'tanitjobs') {
      jobs = await scraper.scrapeJobs(2);
    } else if (source === 'emploi_mg') {
      jobs = await scraper.scrapeJobs(2);
    } else if (source === 'portaljob_mg') {
      jobs = await scraper.scrapeJobs(2);
    }
    
    console.log(JSON.stringify({ jobs, count: jobs.length }, null, 2));
  } catch (error) {
    console.error('Scraping failed:', error.message);
    process.exit(1);
  } finally {
    await scraper.close();
  }
}

main();