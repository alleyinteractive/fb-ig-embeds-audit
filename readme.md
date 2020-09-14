# Facebook/Instagram Embeds Audit Plugin

## About
This plugin adds a new [WP CLI](https://wp-cli.org/) command to audit the instances of Facebook and Instagram oembeds in post content.

## Background Info
Facebook is dropping support for unauthenticated Facebook and Instagram oEmbeds on October 24th.

> oEmbed is a format for allowing an embedded representation of a URL on third party sites. The simple API allows a website to display embedded content (such as photos or videos) when a user posts a link to that resource, without having to parse the resource directly.
> - oembed.com

This means that beginning October 24th, the Facebook and Instagram embeds on all WordPress sites will not display a link to the content, rather than a rendered embed/widget.

**More WordPress Community Info:**
* [WordPress Trac ticket](https://core.trac.wordpress.org/ticket/50861).
* [Gutenberg Ticket](https://github.com/WordPress/gutenberg/issues/24389).

# Getting Started
## Installation
Download this repo to your `/plugins/` folder and activate

## Perform the Audit
Run the command from any WP CLI command line,

**Run the audit on all content**

`$ wp fb-ig-embeds-audit list`

**Target a specific post by id**

`$ wp fb-ig-embeds-audit list --post_id=123`

**Modify the output format**

`$ wp fb-ig-embeds-audit list --format=json`
