/*
  Requirement: Populate the "Weekly Course Breakdown" list page.
*/

// --- Element Selections ---
// TODO: Select the section for the week list ('#week-list-section').
// Using the ID implemented in list.html: #weeks-list-container
const listSection = document.querySelector('#weeks-list-container');

// --- Functions ---

/**
 * TODO: Implement the createWeekArticle function.
 * It takes one week object {id, title, startDate, description}.
 * It should return an <article> element matching the structure in `list.html`.
 * - The "View Details & Discussion" link's `href` MUST be set to `detail.html?id=${id}`.
 * (This is how the detail page will know which week to load).
 */
function createWeekArticle(week) {
    // Create the main container article
    const article = document.createElement('article');
    article.classList.add('week-entry');
    article.setAttribute('data-week-id', week.id); // Useful for styling/tracking

    // Week Title <h2>
    const title = document.createElement('h2');
    title.textContent = week.title;
    article.appendChild(title);

    // Start Date <p>
    const startDateP = document.createElement('p');
    startDateP.classList.add('start-date');
    startDateP.textContent = `Starts on: ${week.startDate}`;
    article.appendChild(startDateP);

    // Description <p> (Using a sliced version for the list view)
    const descriptionP = document.createElement('p');
    descriptionP.classList.add('description');
    // Display only the first 150 characters of the description for brevity
    const shortDescription = week.description.length > 150 
        ? week.description.substring(0, 150) + '...'
        : week.description;
    descriptionP.textContent = shortDescription;
    article.appendChild(descriptionP);

    // Details Link <a>
    const detailsLink = document.createElement('a');
    detailsLink.classList.add('details-link');
    detailsLink.textContent = 'View Details & Discussion';
    // Link MUST be set to detail.html?id=${id}
    detailsLink.href = `detail.html?id=${week.id}`; 
    article.appendChild(detailsLink);

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
        // 1. Use `fetch()` to get data from 'weeks.json'.
        const response = await fetch('weeks.json');
        
        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        // 2. Parse the JSON response into an array.
        const weeksData = await response.json();
        
        // 3. Clear any existing content
        listSection.innerHTML = ''; 

        // 4. Loop through the array and append articles
        if (weeksData && weeksData.length > 0) {
            weeksData.forEach(week => {
                const weekArticle = createWeekArticle(week);
                listSection.appendChild(weekArticle);
            });
        } else {
            listSection.innerHTML = '<p>No weekly course breakdown information is available yet.</p>';
        }

    } catch (error) {
        console.error('Error loading weekly course breakdown:', error);
        listSection.innerHTML = `<p>Failed to load data. Please check 'weeks.json'. Error: ${error.message}</p>`;
    }
}

// --- Initial Page Load ---
// Call the function to populate the page.
loadWeeks();