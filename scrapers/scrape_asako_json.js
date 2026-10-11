const puppeteer = require('puppeteer');

async function main() {
  const browser = await puppeteer.launch({
    headless: true,
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
  
  const page = await browser.newPage();
  await page.setUserAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');
  await page.setViewport({ width: 1280, height: 800 });
  
  try {
    await page.goto('https://asako.mg/emploi', { waitUntil: 'networkidle2', timeout: 30000 });
    await page.waitForSelector('a[href^="/annonces/"]', { timeout: 15000 }).catch(() => {});
    await new Promise(r => setTimeout(r, 3000));
    
    const data = await page.evaluate(() => {
      const jobs = [];
      const links = document.querySelectorAll('a[href^="/annonces/"]');
      
      links.forEach(function(link, index) {
        if (index > 20) return;
        
        const card = link.closest('div');
        if (!card) return;
        
        const linkHref = link.href;
        
        // Extract title
        const titleEl = link.querySelector('[class*="font-heading"]');
        let title = titleEl ? titleEl.textContent?.trim() : link.textContent?.trim() || '';
        
        // Clean up title
        if (title) {
          title = title
            .replace(/\s*·\s*.*?(?:CDI|CDD|Freelance|Stage|Alternance|Sponsorisé).*$/, '')
            .replace(/\s*(?:CDI|CDD|Freelance|Stage|Alternance|Temps\s+(?:plein|partiel)|Sponsorisé).*$/, '')
            .trim();
        }
        
        if (!title || title.length < 3) return;
        
        // Find company
        let company = '';
        const companyCandidates = card.querySelectorAll('[style*="#A78BCD"], [class*="text-sm"][style*="color"]');
        for (const el of companyCandidates) {
          const text = el.textContent?.trim();
          if (text && text.length > 1 && text.length < 100) {
            company = text;
            break;
          }
        }
        
        // Location
        let location = '';
        const mapPins = card.querySelectorAll('svg');
        for (const svg of mapPins) {
          const parent = svg.closest('span, div, p');
          if (parent) {
            const text = parent.textContent?.trim();
            if (text && text.length > 1 && text.length < 50 && !text.includes('Offres') && !text.includes('emploi')) {
              location = text.replace(/.*?[·:]\s*/, '');
              break;
            }
          }
        }
        
        // Contract type
        let contractType = '';
        const badges = card.querySelectorAll('[class*="bg-asako"]');
        for (const badge of badges) {
          const text = badge.textContent?.trim().toUpperCase();
          if (['CDI', 'CDD', 'FREELANCE', 'STAGE', 'ALTERNANCE', 'TEMPS PLEIN', 'TEMPS PARTIEL', 'FULL TIME', 'PART TIME'].some(t => text.includes(t))) {
            contractType = text;
            break;
          }
        }
        
        if (title && title.length > 3) {
          jobs.push({
            title: title.substring(0, 200),
            company: company?.substring(0, 100),
            location: location?.substring(0, 100),
            contractType: contractType?.substring(0, 50),
            url: linkHref
          });
        }
      });
      
      return jobs;
    });
    
    console.log(JSON.stringify({ jobs: data, count: data.length }));
    
    await browser.close();
  } catch (error) {
    console.error('Error:', error.message);
    await browser.close();
    process.exit(1);
  }
}

main().catch(console.error);