/*
  Requirement: Make the "Manage Weekly Breakdown" page interactive.

  Instructions:
  1. Link this file to `admin.html` using:
     <script src="admin.js" defer></script> 
      // NOTE: I'll assume you add 'defer' or place the script tag at the end of <body>.
  
  2. In `admin.html`, add an `id="weeks-tbody"` to the <tbody> element
     inside your `weeks-table`. (Done in the HTML solution).
  
  3. Implement the TODOs below.
*/

// --- Global Data Store ---
// This will hold the weekly data loaded from the JSON file.
let weeks = [];

// --- Element Selections ---
// TODO: Select the week form ('#week-form').
const weekForm = document.querySelector('#week-form');

// TODO: Select the weeks table body ('#weeks-tbody').
// NOTE: I'm selecting by the expected ID for the tbody.
const weeksTableBody = document.querySelector('#weeks-table-body'); // Used #weeks-table-body in the HTML solution

// Select form inputs (needed for handleAddWeek)
const weekIdInput = document.querySelector('#week-id');
const weekTitleInput = document.querySelector('#week-title');
const weekStartDateInput = document.querySelector('#week-start-date');
const weekDescriptionInput = document.querySelector('#week-description');
const weekLinksInput = document.querySelector('#week-links');
const formHeading = document.querySelector('#form-heading');
const submitButton = document.querySelector('#add-week');


// --- Functions ---

/**
 * TODO: Implement the createWeekRow function.
 * It takes one week object {id, title, startDate, description, links}.
 * It should return a <tr> element with the following <td>s:
 * 1. A <td> for the `title`.
* 2. A <td> for the `startDate`.
 * 3. A <td> for a snippet of the `description`.
 * 4. A <td> containing two buttons:
 * - An "Edit" button with class "edit-btn" and `data-id="${id}"`.
 * - A "Delete" button with class "delete-btn" and `data-id="${id}"`.
 */
function createWeekRow(week) {
    const tr = document.createElement('tr');
    tr.dataset.weekId = week.id;

    // Helper to shorten the description
    const snippet = week.description ? week.description.substring(0, 50) + '...' : 'No description.';
    
    // 1. Title
    const titleTd = document.createElement('td');
    titleTd.textContent = week.title;

    // 2. Start Date
    const dateTd = document.createElement('td');
    dateTd.textContent = week.startDate;

    // 3. Description Snippet
    const descTd = document.createElement('td');
    descTd.textContent = snippet;

    // 4. Actions
    const actionsTd = document.createElement('td');
    const editBtn = document.createElement('button');
    editBtn.className = 'edit-btn';
    editBtn.textContent = 'Edit';
    editBtn.dataset.id = week.id;

    const deleteBtn = document.createElement('button');
    deleteBtn.className = 'delete-btn';
    deleteBtn.textContent = 'Delete';
    deleteBtn.dataset.id = week.id;
    deleteBtn.style.marginLeft = '10px'; // Basic styling for separation

    actionsTd.append(editBtn, deleteBtn);

    tr.append(titleTd, dateTd, descTd, actionsTd);
    return tr;
}

/**
 * TODO: Implement the renderTable function.
 * It should:
 * 1. Clear the `weeksTableBody`.
 * 2. Loop through the global `weeks` array.
* 3. Sort the weeks by startDate (optional but helpful).
 * 4. For each week, call `createWeekRow()`, and
 * append the resulting <tr> to `weeksTableBody`.
 */
function renderTable() {
    // 1. Clear the table body
    weeksTableBody.innerHTML = '';
    
    // Sort weeks by startDate (ascending) for better viewing
    const sortedWeeks = [...weeks].sort((a, b) => new Date(a.startDate) - new Date(b.startDate));

    // 2 & 3. Loop and append
    sortedWeeks.forEach(week => {
        const row = createWeekRow(week);
        weeksTableBody.appendChild(row);
    });
}

/**
 * TODO: Implement the handleAddWeek function.
 * This is the event handler for the form's 'submit' event.
 * It should:
 * 1. Prevent the form's default submission.
 * 2. Get the values from the title, start date, and description inputs.
 * 3. Get the value from the 'week-links' textarea. Split this value
 * by newlines (`\n`) to create an array of link strings.
 * 4. Create a new week object with a unique ID (e.g., `id: \`week_${Date.now()}\``).
 * 5. Add this new week object to the global `weeks` array (in-memory only).
 * 6. Call `renderTable()` to refresh the list.
 * 7. Reset the form.
 */
function handleAddWeek(event) {
    // 1. Prevent default submission
    event.preventDefault();

    // 2. Get input values
    const id = weekIdInput.value;
    const title = weekTitleInput.value.trim();
    const startDate = weekStartDateInput.value; // YYYY-MM-DD format
    const description = weekDescriptionInput.value.trim();

    // 3. Process links: split by newline and filter out empty strings
    const linksText = weekLinksInput.value.trim();
    const links = linksText 
        ? linksText.split('\n').map(link => link.trim()).filter(link => link.length > 0) 
        : [];
    
    // The link processing might need refinement depending on the final JSON format, 
    // but assuming a simple array of strings is sufficient for now.
    
    if (id) {
        // --- EDIT LOGIC ---
        const weekIndex = weeks.findIndex(w => w.id === id);
        if (weekIndex !== -1) {
            weeks[weekIndex] = {
                id,
                title,
                startDate,
                description,
                links
            };
            
            // Reset form for "Add" mode
            weekIdInput.value = '';
            formHeading.textContent = 'Add a New Week';
            submitButton.textContent = 'Add Week';
        }
    } else {
        // --- ADD LOGIC ---
        // 4. Create a new week object with a unique ID
        const newWeek = {
            // Using a simple unique ID structure: week_[timestamp]
            id: `week_${Date.now()}`,
            title,
            startDate,
            description,
            links
        };
        
        // 5. Add to global array
        weeks.push(newWeek);
    }
    
    // 6. Refresh the list
    renderTable();

    // 7. Reset the form (clears inputs for the next entry/action)
    weekForm.reset();
}


/**
 * Helper function for handling the "Edit" button click.
 * Loads the selected week's data into the form for editing.
 */
function handleEditWeek(id) {
    const weekToEdit = weeks.find(w => w.id === id);
    if (!weekToEdit) return;

    // Populate the form fields with the week's data
    weekIdInput.value = weekToEdit.id;
    weekTitleInput.value = weekToEdit.title;
    weekStartDateInput.value = weekToEdit.startDate;
    weekDescriptionInput.value = weekToEdit.description;
    
    // Join the links array back into a newline-separated string for the textarea
    weekLinksInput.value = weekToEdit.links.join('\n');
    
    // Update UI elements to reflect editing mode
    formHeading.textContent = `Edit Week: ${weekToEdit.title}`;
    submitButton.textContent = 'Update Week';

    // Scroll to the form for better UX
    formHeading.scrollIntoView({ behavior: 'smooth' });
}


/**
 * TODO: Implement the handleTableClick function.
 * This is an event listener on the `weeksTableBody` (for delegation).
 * It should:
 * 1. Check if the clicked element (`event.target`) has the class "delete-btn" or "edit-btn".
 * 2. If it is a delete button, get the `data-id` attribute from the button.
 * 3. Update the global `weeks` array by filtering out the week
 * with the matching ID (in-memory only).
 * 4. Call `renderTable()` to refresh the list.
 */
function handleTableClick(event) {
    const target = event.target;
    const weekId = target.dataset.id;

    if (!weekId) return; // Exit if the clicked element has no ID (e.g., the table row or empty space)

    // 1. Check if the clicked element is a delete button
    if (target.classList.contains('delete-btn')) {
        if (!confirm(`Are you sure you want to delete week ${weekId}? This action cannot be undone.`)) {
            return;
        }

        // 3. Update global array by filtering (DELETION)
        weeks = weeks.filter(week => week.id !== weekId);
        
        // 4. Refresh the list
        renderTable();

    } else if (target.classList.contains('edit-btn')) {
        // Handle Edit functionality
        handleEditWeek(weekId);
    }
}


/**
 * This function needs to be 'async'.
 * It should:
 * 1. Use `fetch()` to get data from 'weeks.json'.
 * 2. Parse the JSON response and store the result in the global `weeks` array.
 * 3. Call `renderTable()` to populate the table for the first time.
 * 4. Add the 'submit' event listener to `weekForm` (calls `handleAddWeek`).
 * 5. Add the 'click' event listener to `weeksTableBody` (calls `handleTableClick`).
 */
async function loadAndInitialize() {
    try {
    // 1. Fetch data from the API folder 'api/weeks.json'
    const response = await fetch('api/weeks.json');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        // 2. Parse JSON and store in global array
        const data = await response.json();
        // Assuming the data is an array of week objects
        weeks = data; 
        
    } catch (error) {
        console.error('Failed to load weeks data:', error);
        // Fallback: Initialize with an empty array or dummy data if fetch fails
        weeks = []; 
    }

    // 3. Populate the table
    renderTable();

    // 4. Add the 'submit' event listener to `weekForm`
    if (weekForm) {
        weekForm.addEventListener('submit', handleAddWeek);
    }

    // 5. Add the 'click' event listener to `weeksTableBody` (for delegation)
    if (weeksTableBody) {
        weeksTableBody.addEventListener('click', handleTableClick);
    }
}

// --- Initial Page Load ---
// Call the main async function to start the application.
loadAndInitialize();