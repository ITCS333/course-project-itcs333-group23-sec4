<!-- STANDARD TAILWIND NAVBAR + HEADER PATTERN FOR COURSE PROJECT -->

<!-- Body: -->
<body class="bg-primary-900 text-paper">

<!-- Navigation Bar (use this pattern on all pages; update nav links as needed) -->
<nav class="flex items-center justify-between bg-gradient-to-r from-primary-800 to-primary px-6 py-4 shadow-lg">
  <a class="text-2xl font-bold text-white tracking-wide" href="../../index.html">Course Dashboard</a>
  <ul class="flex gap-8">
    <li><a class="text-gray-200 font-medium hover:text-white transition border-b-2 border-primary-400" href="CURRENT_PAGE.html">CURRENT_PAGE</a></li>
    <li><a class="text-gray-200 font-medium hover:text-white transition" href="../weekly/list.html">Weekly</a></li>
    <li><a class="text-gray-200 font-medium hover:text-white transition" href="../assignments/list.html">Assignments</a></li>
    <li><a class="text-gray-200 font-medium hover:text-white transition" href="../resources/list.html">Resources</a></li>
    <li><a class="text-gray-200 font-medium hover:text-white transition" href="../discussion/board.html">Discussion</a></li>
    <li><a class="text-gray-200 font-medium hover:text-white transition" href="../admin/manage_users.html">Admin</a></li>
  </ul>
</nav>

<!-- Header (use this for section title pages) -->
<header class="max-w-6xl mx-auto px-6 py-8 border-b-2 border-primary">
    <h1 class="text-4xl font-bold text-white mb-2">PAGE TITLE HERE</h1>
</header>

<!-- Main Content Container -->
<main class="max-w-6xl mx-auto px-6 py-8">
  <!-- Add page-specific content here -->
</main>

</body>

<!-- ============================================================================ -->
<!-- CARD COMPONENT PATTERN (for lists of items) -->
<!-- ============================================================================ -->

<!-- Card: -->
<article class="bg-primary-800 border-l-4 border-gold rounded-lg p-6 shadow-md hover:shadow-lg transition">
    <h2 class="text-xl font-bold text-white mb-3">Card Title</h2>
    <div class="flex gap-3 mb-3">
        <span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Status</span>
        <span class="text-gray-300 text-sm">Meta Info</span>
    </div>
    <p class="text-gray-300 mb-4">Card description or content goes here.</p>
    <div class="flex items-center justify-between">
        <div class="w-32 h-2 bg-primary-700 rounded-full">
            <div class="h-full bg-gold rounded-full" style="width:50%"></div>
        </div>
        <a href="#" class="bg-gold hover:bg-accent text-white font-semibold px-4 py-2 rounded-lg transition">Action</a>
    </div>
</article>

<!-- Grid Container (for multiple cards) -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <!-- Cards go here -->
</section>

<!-- ============================================================================ -->
<!-- BUTTON PATTERNS -->
<!-- ============================================================================ -->

<!-- Primary Button -->
<button class="bg-gold hover:bg-accent text-white font-semibold px-4 py-2 rounded-lg transition">Click Me</button>

<!-- Secondary Button -->
<button class="bg-primary hover:bg-primary-600 text-white font-semibold px-4 py-2 rounded-lg transition">Click Me</button>

<!-- Tertiary Button (Ghost) -->
<button class="border-2 border-gold text-gold hover:bg-gold hover:text-primary font-semibold px-4 py-2 rounded-lg transition">Click Me</button>

<!-- ============================================================================ -->
<!-- FORM ELEMENTS -->
<!-- ============================================================================ -->

<!-- Input Field -->
<input type="text" placeholder="Enter text..." class="w-full px-4 py-2 rounded-lg bg-primary-800 border border-primary-600 text-white placeholder-gray-400 focus:outline-none focus:border-gold">

<!-- Textarea -->
<textarea placeholder="Enter message..." class="w-full px-4 py-2 rounded-lg bg-primary-800 border border-primary-600 text-white placeholder-gray-400 focus:outline-none focus:border-gold"></textarea>

<!-- ============================================================================ -->
<!-- BADGE PATTERNS -->
<!-- ============================================================================ -->

<!-- Badge (Primary) -->
<span class="bg-primary-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Open</span>

<!-- Badge (Success) -->
<span class="bg-universityGreen-600 text-white text-xs font-semibold px-3 py-1 rounded-full">Complete</span>

<!-- Badge (Accent) -->
<span class="bg-gold text-primary-900 text-xs font-semibold px-3 py-1 rounded-full">Featured</span>

<!-- ============================================================================ -->
<!-- TEXT & TYPOGRAPHY -->
<!-- ============================================================================ -->

<!-- Heading 1 -->
<h1 class="text-4xl font-bold text-white">Heading 1</h1>

<!-- Heading 2 -->
<h2 class="text-2xl font-bold text-white">Heading 2</h2>

<!-- Heading 3 -->
<h3 class="text-xl font-bold text-white">Heading 3</h3>

<!-- Body Text (Primary) -->
<p class="text-gray-300">Normal paragraph text</p>

<!-- Body Text (Muted) -->
<p class="text-gray-400">Muted paragraph text</p>

<!-- Link -->
<a href="#" class="text-gold hover:text-accent transition">Link Text</a>

<!-- ============================================================================ -->
<!-- LAYOUT UTILITIES -->
<!-- ============================================================================ -->

<!-- Flex Container (horizontal) -->
<div class="flex items-center justify-between gap-4">
  <!-- Content here -->
</div>

<!-- Grid Container -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  <!-- Cards here -->
</div>

<!-- Spacing: Padding -->
<div class="px-6 py-8">Content with horizontal padding 6, vertical padding 8</div>

<!-- Spacing: Margin -->
<div class="mx-auto mb-8">Content with auto horizontal margin, bottom margin 8</div>

<!-- Max Width Container -->
<div class="max-w-6xl mx-auto px-6">Constrained width content</div>
