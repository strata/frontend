# About

The project was created in 2019 by [Studio 24](https://www.studio24.net/) to help create websites based on a
[Headless CMS architecture](https://www.studio24.net/blog/what-is-a-headless-cms/), initially supporting
[WordPress](https://wordpress.org/) content APIs.

In 2021 Strata was refactored to extract the data layer into [Strata Data](https://github.com/strata/data)
and to more easily support different APIs. GraphQL support was added to help power the new [W3C](https://www.w3.org/)
website.

As an architecture option, Headless gives a lot of flexibility since you can develop the front-end in any way you wish.
It also increases security and performance by only building what you need on the frontend. More often that not, the
industry uses JavaScript focussed solutions with Headless that requires large payloads of
JavaScript to render content-based pages (e.g. single page apps).

This isn't how Studio 24 believes the web should be built. We have a fantastic technology for content-driven pages:
HTML. We believe core content should be delivered over HTML with JavaScript used with
[progressive enhancement](https://www.gov.uk/service-manual/technology/using-progressive-enhancement) to add
interactivity or personalisation to pages.

Strata is a Symfony-based application to support efficient delivery of content from multiple data sources. While there is
a heavy focus on delivering cached HTML pages to users, it's simple to add dynamic content when you need it.

Headless also comes with its flaws, such as content preview. We're building tools to help overcome some of the
difficulties of headless.

We've open sourced this project to both ensure our public sector clients can use it and to share it with the community.
We hope you find it useful! 
