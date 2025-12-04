/*
  Requirement: Make the "Manage Resources" page interactive.

  Instructions:
  1. Link this file to `admin.html` using:
     <script src="admin.js" defer></script>
  
  2. In `admin.html`, add an `id="resources-tbody"` to the <tbody> element
     inside your `resources-table`.
  
  3. Implement the TODOs below.
*/

// --- Global Data Store ---
// This will hold the resources loaded from the JSON file.
let resources = [];

// --- Element Selections ---
// Select the resource form ('#resource-form').
const resourceForm = document.querySelector('#resource-form');

// Select the resources table body ('#resources-tbody').
const resourcesTableBody = document.querySelector('#resources-tbody');

// --- Functions ---

/**
 * Implement the createResourceRow function.
 */
function createResourceRow(resource) {
  const tr = document.createElement('tr');
  
  const titleTd = document.createElement('td');
  titleTd.textContent = resource.title;
  
  const descriptionTd = document.createElement('td');
  descriptionTd.textContent = resource.description;
  
  const actionsTd = document.createElement('td');
  
  const editBtn = document.createElement('button');
  editBtn.textContent = 'Edit';
  editBtn.className = 'edit-btn';
  editBtn.setAttribute('data-id', resource.id);
  
  const deleteBtn = document.createElement('button');
  deleteBtn.textContent = 'Delete';
  deleteBtn.className = 'delete-btn';
  deleteBtn.setAttribute('data-id', resource.id);
  
  actionsTd.appendChild(editBtn);
  actionsTd.appendChild(deleteBtn);
  
  tr.appendChild(titleTd);
  tr.appendChild(descriptionTd);
  tr.appendChild(actionsTd);
  
  return tr;
}

/**
 * Implement the renderTable function.
 */
function renderTable() {
  resourcesTableBody.innerHTML = '';
  
  resources.forEach(resource => {
    const row = createResourceRow(resource);
    resourcesTableBody.appendChild(row);
  });
}

/**
 * Implement the handleAddResource function.
 */
function handleAddResource(event) {
  event.preventDefault();
  
  const title = document.querySelector('#resource-title').value;
  const description = document.querySelector('#resource-description').value;
  const link = document.querySelector('#resource-link').value;
  
  const newResource = {
    id: `res_${Date.now()}`,
    title: title,
    description: description,
    link: link
  };
  
  resources.push(newResource);
  
  renderTable();
  
  resourceForm.reset();
}

/**
 * Implement the handleTableClick function.
 */
function handleTableClick(event) {
  if (event.target.classList.contains('delete-btn')) {
    const resourceId = event.target.getAttribute('data-id');
    
    resources = resources.filter(resource => resource.id !== resourceId);
    
    renderTable();
  }
}

/**
 * Implement the loadAndInitialize function.
 */
async function loadAndInitialize() {
  const response = await fetch('resources.json');
  
  const data = await response.json();
  
  resources = data;
  
  renderTable();
  
  resourceForm.addEventListener('submit', handleAddResource);
  
  resourcesTableBody.addEventListener('click', handleTableClick);
}

// --- Initial Page Load ---
// Call the main async function to start the application.
loadAndInitialize();
