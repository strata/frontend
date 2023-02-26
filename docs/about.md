# About

The project was created in 2019 by [Studio 24](https://www.studio24.net/) to help create websites based on a
[Headless CMS architecture](https://www.studio24.net/blog/what-is-a-headless-cms/), initially supporting
[WordPress](https://wordpress.org/) content APIs.

In 2021 Strata was refactored to extract the data layer into [Strata Data](https://github.com/strata/data)
and to more easily support different APIs. GraphQL support was added to help power the new [W3C](https://www.w3.org/)
website.

As an architecture option, headless gives a lot of flexibility since you can develop the front-end in any way you wish.
It also increases security and performance by only building what you need on the frontend. 

Studio 24 believe in HTML-first websites with server-rendered web pages. It's an approach that even modern JavaScript 
frameworks are coming back to and one that PHP has done very well for years.

We believe core content should be delivered over HTML with JavaScript used with
[progressive enhancement](https://www.gov.uk/service-manual/technology/using-progressive-enhancement) to add
interactivity or personalisation to pages.

Strata is a [Symfony-based](https://symfony.com/) application to support efficient delivery of content from multiple 
data sources. While there is a heavy focus on delivering cached HTML pages to users, it's simple to add dynamic 
content when you need it. It can also be used standalone or with other frameworks, such as Laravel.

Headless also comes with its flaws, such as content preview. We're building tools to help overcome some of the
difficulties of headless.

We've open sourced this project to both ensure our clients can use it and to share it with the community.
We hope you find it useful! 
