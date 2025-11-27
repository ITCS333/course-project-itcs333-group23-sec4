/*
  Requirement: Populate the "Weekly Course Breakdown" list page.

  Instructions:
  (Assumes all required IDs were added to list.html as specified in the instructions.)
*/

// --- Element Selections ---
// TODO: Select the section for the week list ('#weekly-list'). 
// NOTE: Using '#weekly-list' based on the list.html solution provided previously.
const listSection = document.querySelector('#weekly-list');

// --- Functions ---

/**
 * TODO: Implement the createWeekArticle function.
 * It takes one week object {id, title, startDate, description}.
 * It should return an <article> element matching the structure in `list.html`.
 * - The "View Details & Discussion" link's `href` MUST be set to `details.html?weekId=${id}`.
 * (Using 'weekId' to match the detail.js implementation).
 */
function createWeekArticle(week) {
    // Create the main container article
    const article = document.createElement('article');
    article.classList.add('week-summary');
    article.dataset.weekId = week.id;

    // 1. Heading (h2)
    const h2 = document.createElement('h2');
    h2.textContent = week.title;

    // 2. Start Date (p)
    const pDate = document.createElement('p');
    pDate.classList.add('start-date');
    pDate.textContent = `Starts on: ${week.startDate}`;

    // 3. Description Snippet (p) - using first 150 characters for brevity
    const pDesc = document.createElement('p');
    pDesc.classList.add('description-snippet');
    const snippet = week.description ? week.description.substring(0, 150) + (week.description.length > 150 ? '...' : '') : 'No description available.';
    pDesc.textContent = snippet;

    // 4. Details Link (a)
    const aLink = document.createElement('a');
    aLink.classList.add('details-link');
    // Set the href with the week's ID in the query string (point to 'details.html')
    aLink.href = `details.html?weekId=${week.id}`; 
    aLink.textContent = 'View Details & Discussion →';

    // Assemble the article
    article.append(h2, pDate, pDesc, aLink);
    
    return article;
}

/**
 * TODO: Implement the loadWeeks function.
 * This function needs to be 'async'.
 * It should:
 * 1. Use `fetch()` to get data from 'weeks.json'.
 * 2. Parse the JSON response into an array.
 * 3. Clear any existing content from `listSection`.
 * 4. Loop through the weeks array. For each week:
 * - Call `createWeekArticle()`.
 * - Append the returned <article> element to `listSection`.
 */
async function loadWeeks() {
    try {
    // 1. Fetch data from the API folder 'api/weeks.json'
    const response = await fetch('api/weeks.json');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // 2. Parse the JSON response
        let weeks = await response.json();
        
        // Optional: Sort weeks by start date before rendering
        weeks.sort((a, b) => new Date(a.startDate) - new Date(b.startDate));

        // 3. Clear existing content (like the dummy data)
        listSection.innerHTML = ''; 

        // 4. Loop, create, and append
        if (weeks && weeks.length > 0) {
            weeks.forEach(week => {
                    // Ensure the details link points to the correct page and uses the week id
                    // createWeekArticle builds the article including the details link
                    const weekArticle = createWeekArticle(week);
                    // adjust details link to point to 'details.html' (file name in repo)
                    const detailsLink = weekArticle.querySelector('.details-link');
                    if (detailsLink) detailsLink.href = `details.html?weekId=${week.id}`;
                    listSection.appendChild(weekArticle);
            });
        } else {
            // Display message if no weeks are loaded
            listSection.innerHTML = '<p>No weekly course breakdown information is available yet.</p>';
        }

    } catch (error) {
        console.error('Failed to load weekly breakdown:', error);
        listSection.innerHTML = '<p>Error loading course content. Please try again later.</p>';
    }
}

// --- Initial Page Load ---
// Call the function to populate the page.
loadWeeks();