# Code Prettifier for WordPress
Yet another WordPress plugin that relies on Google's [JS Code Prettifier](https://github.com/google/code-prettify) to highlight code snippets in blog posts.

This plugin, however, uses neither regular expressions nor JavaScript to trigger the highlighter script. Instead, it relies on PHP's built-in `DOMDocument` class to add the required `class` to preformatted code blocks in blog posts and pages. _A future version may actually use JavaScript (like Code Prettifier itself) as a trigger._
