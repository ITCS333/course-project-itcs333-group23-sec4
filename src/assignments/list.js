/*
  Requirement: Populate the "Course Assignments" list page.

  Instructions:
  1. Link this file to `list.html` using:
     <script src="list.js" defer></script>

  2. In `list.html`, add an `id="assignment-list-section"` to the
     <section> element that will contain the assignment articles.

  3. Implement the TODOs below.
*/

// --- Element Selections ---
// TODO: Select the section for the assignment list ('#assignment-list-section').
const listSection = document.querySelector('#assignment-list-section');

// --- Functions ---

/**
 * TODO: Implement the createAssignmentArticle function.
 * It takes one assignment object {id, title, dueDate, description}.
 * It should return an <article> element matching the structure in `list.html`.
 * The "View Details" link's `href` MUST be set to `details.html?id=${id}`.
 * This is how the detail page will know which assignment to load.
 */
function createAssignmentArticle(assignment) {
  const article = document.createElement('article');
  article.className = 'assignment-card professional';
  article.setAttribute('data-id', assignment.id);
  
  // Create header
  const header = document.createElement('div');
  header.className = 'professional-card-header';
  
  const icon = document.createElement('div');
  icon.className = 'professional-card-icon';
  icon.textContent = '📋';
  
  const titleGroup = document.createElement('div');
  titleGroup.className = 'professional-card-title-group';
  
  const title = document.createElement('h2');
  title.textContent = assignment.title;
  
  const badgeGroup = document.createElement('div');
  badgeGroup.className = 'badge-group';
  
  const statusBadge = document.createElement('span');
  statusBadge.className = 'badge-status pending';
  statusBadge.textContent = `📅 Due: ${assignment.dueDate}`;
  
  badgeGroup.appendChild(statusBadge);
  titleGroup.appendChild(title);
  titleGroup.appendChild(badgeGroup);
  header.appendChild(icon);
  header.appendChild(titleGroup);
  
  // Create body
  const body = document.createElement('div');
  body.className = 'professional-card-body';
  
  const description = document.createElement('p');
  description.className = 'professional-card-description';
  description.textContent = assignment.description;
  
  body.appendChild(description);
  
  // Create footer
  const footer = document.createElement('div');
  footer.className = 'professional-card-footer';
  
  const dueDatePill = document.createElement('span');
  dueDatePill.className = 'due-date-pill';
  dueDatePill.textContent = `Due: ${assignment.dueDate}`;
  
  const link = document.createElement('a');
  link.href = `details.html?id=${assignment.id}`;
  link.className = 'professional-card-action';
  link.textContent = 'View Details';
  
  footer.appendChild(dueDatePill);
  footer.appendChild(link);
  
  // Assemble card
  article.appendChild(header);
  article.appendChild(body);
  article.appendChild(footer);
  
  return article;
}

/**
 * TODO: Implement the loadAssignments function.
 * This function needs to be 'async'.
 * It should:
 * 1. Use `fetch()` to get data from 'assignments.json'.
 * 2. Parse the JSON response into an array.
 * 3. Clear any existing content from `listSection`.
 * 4. Loop through the assignments array. For each assignment:
 * - Call `createAssignmentArticle()`.
 * - Append the returned <article> element to `listSection`.
 */
async function loadAssignments() {
  try {
    // fetch assignments from the local api folder
    const response = await fetch('https://4f145ee4-165d-410b-bd56-629bd198bb0f-00-mllyp4mgogim.pike.replit.dev/src/assignments/api/index.php?resource=assignments');
    if (!response.ok) throw new Error(`HTTP ${response.status}`);
    const assignments = (await response.json())?.data;

    if (!listSection) return; // guard if section is missing

    listSection.innerHTML = '';
    if (!assignments || assignments.length === 0) {
      const empty = document.createElement('p');
      empty.className = 'text-gray-300';
      empty.textContent = 'No assignments available.';
      listSection.appendChild(empty);
      return;
    }

    assignments.forEach(assignment => {
      const article = createAssignmentArticle(assignment);
      listSection.appendChild(article);
    });
  } catch (err) {
    console.error('Failed to load assignments:', err);
    if (listSection) {
      listSection.innerHTML = '';
      const errEl = document.createElement('p');
      errEl.className = 'text-red-400';
      errEl.textContent = 'Error loading assignments.';
      listSection.appendChild(errEl);
    }
  }
}

// --- Initial Page Load ---
// Call the function to populate the page.
loadAssignments();
