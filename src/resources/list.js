/*
  Requirement: Populate the "Course Resources" list page.

  Instructions:
  1. Link this file to `list.html` using:
     <script src="list.js" defer></script>

  2. In `list.html`, add an `id="resource-list-section"` to the
     <section> element that will contain the resource articles.

  3. Implement the TODOs below.
*/

// --- Element Selections ---
// Select the section for the resource list ('#resource-list-section').
const listSection = document.querySelector('#resource-list-section');

// --- Functions ---

/**
 * Implement the createResourceArticle function.
 */
function createResourceArticle(resource) {
  const article = document.createElement('article');
  
  const heading = document.createElement('h2');
  heading.textContent = resource.title;
  
  const description = document.createElement('p');
  description.textContent = resource.description;
  
  const link = document.createElement('a');
  link.href = `details.html?id=${resource.id}`;
  link.textContent = 'View Resource & Discussion';
  
  article.appendChild(heading);
  article.appendChild(description);
  article.appendChild(link);
  
  return article;
}

/**
 * Implement the loadResources function.
 */
async function loadResources() {
  const response = await fetch('resources.json');
  
  const resources = await response.json();
  
  listSection.innerHTML = '';
  
  resources.forEach(resource => {
    const article = createResourceArticle(resource);
    listSection.appendChild(article);
  });
}

// --- Initial Page Load ---
// Call the function to populate the page.
loadResources();