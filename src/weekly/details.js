/*
  Requirement: Populate the weekly detail page and discussion forum.

  Instructions:
  (Assumes all required IDs were added to detail.html as specified in the instructions.)
*/

// --- Global Data Store ---
// These will hold the data related to *this* specific week.
let currentWeekId = null;
let currentComments = [];

// --- Element Selections ---
// TODO: Select all the elements you added IDs for in step 2.
const weekTitleHeading = document.querySelector('#week-title-heading');
const weekStartDateElement = document.querySelector('#week-start-date');
const weekDescriptionElement = document.querySelector('#week-description');
const weekLinksList = document.querySelector('#week-resources-list'); // Renamed from instructions to match detail.html solution
const commentList = document.querySelector('#comments-container'); // Renamed from instructions to match detail.html solution
const commentForm = document.querySelector('#new-comment-form'); // Renamed from instructions to match detail.html solution
const newCommentTextarea = document.querySelector('#new-comment'); // Renamed from instructions to match detail.html solution
const commentAuthorInput = document.querySelector('#comment-author');

// --- Functions ---

/**
 * TODO: Implement the getWeekIdFromURL function.
 * It should:
 * 1. Get the query string from `window.location.search`.
 * 2. Use the `URLSearchParams` object to get the value of the 'id' parameter.
 * 3. Return the id.
 */
function getWeekIdFromURL() {
    // 1. Get the query string
    const urlParams = new URLSearchParams(window.location.search);
    
    // 2. Get the value of the 'weekId' parameter (assuming 'weekId' based on common URL practices and list.html solution)
    // If the instruction meant 'id', change 'weekId' to 'id'. I'll use 'weekId' for clarity.
    return urlParams.get('weekId');
}

/**
 * TODO: Implement the renderWeekDetails function.
 * It takes one week object.
 * It should:
 * 1. Set the `textContent` of `weekTitle` to the week's title.
 * 2. Set the `textContent` of `weekStartDate` to "Starts on: " + week's startDate.
 * 3. Set the `textContent` of `weekDescription`.
 * 4. Clear `weekLinksList` and then create and append `<li><a href="...">...</a></li>`
 * for each link in the week's 'links' array. The link's `href` and `textContent`
 * should both be the link URL.
 */
function renderWeekDetails(week) {
    // 1. Set the title
    weekTitleHeading.textContent = week.title;

    // 2. Set the start date
    weekStartDateElement.textContent = `Starts on: ${week.startDate}`;

    // 3. Set the description
    weekDescriptionElement.textContent = week.description;

    // 4. Handle links/resources
    weekLinksList.innerHTML = ''; // Clear previous items

    if (week.links && week.links.length > 0) {
        week.links.forEach(linkUrl => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            
            a.href = linkUrl;
            // Use the full URL as text, or try to infer a title if necessary (keeping it simple as per instructions)
            a.textContent = linkUrl; 
            a.target = '_blank'; // Open links in a new tab
            
            li.appendChild(a);
            weekLinksList.appendChild(li);
        });
    } else {
        const li = document.createElement('li');
        li.textContent = 'No exercises or resources provided for this week.';
        weekLinksList.appendChild(li);
    }
}

/**
 * TODO: Implement the createCommentArticle function.
 * It takes one comment object {author, text, timestamp (optional)}.
 * It should return an <article> element matching the structure in `details.html`.
 * (e.g., an <article> containing a <p> and a <footer>).
 */
function createCommentArticle(comment) {
    const article = document.createElement('article');
    article.classList.add('comment');
    
    // Comment Text
    const p = document.createElement('p');
    p.textContent = comment.text;

    // Comment Footer (Author and Date)
    const footer = document.createElement('footer');
    const date = comment.timestamp ? new Date(comment.timestamp).toLocaleDateString() : 'N/A';
    footer.innerHTML = `Posted by: <strong>${comment.author || 'Anonymous'}</strong> on <time>${date}</time>`;

    article.append(p, footer);
    return article;
}

/**
 * TODO: Implement the renderComments function.
 * It should:
 * 1. Clear the `commentList`.
 * 2. Loop through the global `currentComments` array.
 * 3. For each comment, call `createCommentArticle()`, and
 * append the resulting <article> to `commentList`.
 */
function renderComments() {
    // 1. Clear the comment list
    commentList.innerHTML = '';

    if (currentComments.length === 0) {
        const p = document.createElement('p');
        p.textContent = 'No comments yet. Be the first to start the discussion!';
        commentList.appendChild(p);
        return;
    }

    // 2 & 3. Loop and append
    currentComments.forEach(comment => {
        const commentArticle = createCommentArticle(comment);
        commentList.appendChild(commentArticle);
    });
}

/**
 * TODO: Implement the handleAddComment function.
 * This is the event handler for the `commentForm` 'submit' event.
 * It should:
 * 1. Prevent the form's default submission.
 * 2. Get the text from `newCommentTextarea.value`.
 * 3. If the text is empty, return.
 * 4. Create a new comment object: { author: 'Student', text: commentText, timestamp: Date.now() }
 * (Using the optional author field from the HTML solution).
 * 5. Add the new comment to the global `currentComments` array (in-memory only).
 * 6. Call `renderComments()` to refresh the list.
 * 7. Clear the `newCommentTextarea` and `commentAuthorInput`.
 */
function handleAddComment(event) {
    // 1. Prevent the form's default submission
    event.preventDefault();

    // 2. Get input values
    const commentText = newCommentTextarea.value.trim();
    const authorName = commentAuthorInput.value.trim() || 'Anonymous Student';

    // 3. If the text is empty, return
    if (!commentText) {
        alert('Please enter a comment before posting.');
        return;
    }

    // 4. Create a new comment object
    const newComment = {
        author: authorName,
        text: commentText,
        timestamp: Date.now()
    };

    // 5. Add the new comment to the global array
    currentComments.push(newComment);

    // 6. Refresh the list
    renderComments();

    // 7. Clear the form fields
    newCommentTextarea.value = '';
    commentAuthorInput.value = '';
}

/**
 * TODO: Implement an `initializePage` function.
 * This function needs to be 'async'.
 * It should:
 * 1. Get the `currentWeekId` by calling `getWeekIdFromURL()`.
 * 2. If no ID is found, set `weekTitle.textContent = "Week not found."` and stop.
 * 3. `fetch` both 'weeks.json' and 'comments.json' (you can use `Promise.all`).
 * 4. Parse both JSON responses.
 * 5. Find the correct week from the weeks array using the `currentWeekId`.
 * 6. Get the correct comments array from the comments object using the `currentWeekId`.
 * Store this in the global `currentComments` variable. (If no comments exist, use an empty array).
 * 7. If the week is found:
 * - Call `renderWeekDetails()` with the week object.
 * - Call `renderComments()` to show the initial comments.
 * - Add the 'submit' event listener to `commentForm` (calls `handleAddComment`).
 * 8. If the week is not found, display an error in `weekTitle`.
 */
async function initializePage() {
    // 1. Get the currentWeekId
    currentWeekId = getWeekIdFromURL();

    // 2. Check for ID
    if (!currentWeekId) {
        weekTitleHeading.textContent = "Error: Weekly content ID not found.";
        return;
    }

    try {
        // 3. Fetch data concurrently
        const [weeksResponse, commentsResponse] = await Promise.all([
            fetch('api/weeks.json'),
            fetch('api/comments.json')
        ]);

        if (!weeksResponse.ok || !commentsResponse.ok) {
            throw new Error('Failed to fetch one or more data files.');
        }

        // 4. Parse JSON responses
    const weeksData = await weeksResponse.json();
    const commentsData = await commentsResponse.json(); // commentsData is expected to be an object mapping IDs to arrays

    // 5. Find the correct week (ensure type-safe comparison)
    const week = weeksData.find(w => String(w.id) === String(currentWeekId));
        
        // 6. Get the correct comments array
        currentComments = commentsData[currentWeekId] || [];

        if (week) {
            // 7a. Render week details
            renderWeekDetails(week);
            
            // 7b. Render initial comments
            renderComments();

            // 7c. Add event listener
            if (commentForm) {
                commentForm.addEventListener('submit', handleAddComment);
            }

        } else {
            weekTitleHeading.textContent = `Error: Week content for ID "${currentWeekId}" not found.`;
        }

    } catch (error) {
        console.error('Initialization failed:', error);
        weekTitleHeading.textContent = 'Error loading course content.';
    }
}

// --- Initial Page Load ---
initializePage();