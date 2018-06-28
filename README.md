# WPM Toolkit
Accessing all of WPM features begins with a wpm function call. This function takes a single argument to tell wpm the kind of information you would like to access. From here, chain calls to build up a request. We'll get into greater detail later. For now, here are the different arguments available to you.

## Basics
### wp.
wp allows you to integrate with WordPress by adding Post Types, Taxonomies, Sorting, Actions, and Filters. Here's an example of each.

```php
// add a post type
wpm('wp.post_type')->create([
    'name'      => 'Member'
]);

// add a taxonomy
wpm('wp.taxonomy')->create([
    'name'       => 'Role',
    'post_type'  => 'member'
]);

// add sorting to post types
wpm('sort')->add(['page', 'member']);

// add an action
wpm('wp.action')->add('get_footer', function() {
    loadPopUps();
});

// add a filter
wpm('wp.filter')->add('the_title', function($title) {
    return '<strong>' . $title . '</strong>';
});
```

### q.
q, which is short for query, will replace the chaotic mess that is get_posts and WP_Query. You'll get most of your time savings here. I'll get into how much better this is later but, for now, here are a few simple examples.

```php
// get all of the faqs
$faqs = wpm('q.faq')->get();

// get all of the author named "chip" 's posts
$author_posts = wpm('q.post')->author('chip')->get();

// get all members that have the role of "ceo"
$ceo_members = wpm('q.member')->tax('role', 'ceo')->get();

// get all members that are old enough to vote
$voters = wpm('q.member')->meta('age', '>=', 18)->get();

// find a member named Jenny Doe
$member = wpm('q.member')->find('Jenny Doe');
```


### img
Pre-planning image sizes is a hassle especially when you're working with a layout that requires a lot of options. If this sounds like you, img is your new best friend. It is non-destructive, creates version on the fly, and caches them for later use.

```php
// use featured image or Advanced Custom Fields to get the ID of an image then...
// display an image that fits within a width of 300
echo wpm('img')->media($image_id)->fit(300)->get();

// display an image cropped to 500x300
echo wpm('img')->media($image_id)->resize(500, 300)->get();

// get the url of an image resized to a height of 300 and the proportional width
$url = wpm('img')->media($image_id)->resize(null, 300)->url();
```


### store.
Use store when you need to hold on to some data. You can store it for the rest of the page load or tuck it away in the database. Data stored in the database will be serialized.

```php
<?php
// VARIABLE
// store a color value
wpm('store.var')->set('color', '#dedede');
?>

<!-- Call it when you need it -->
<div style="background-color: <?php echo wpm('store.var')->get('color') ?>">
    Hello World!
</div>

<?php
// DATABASE
// store something to the database
wpm('store.db')->set('contact-preference', 'email');

// get value
wpm('store.db')->get('contact-preference');

// COOKIE
// store something in a cookie
wpm('store.cookie')->set('collapse-sidebar', 1);

// get value from cookie
wpm('store.cookie')->get('collapse-sidebar');
```


### Read the Docs
That's probably enough to get you on your way. [Dive into the docs](https://wpmachine.co/documentation/v2/wpm-toolkit/) to see the full feature set.
