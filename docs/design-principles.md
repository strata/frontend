# Design principles

Strata is designed to deliver fast, accessible user experiences on the modern web.

New features should be reviewed against our design principles:

* **Simplify front-end development** - Freedom on the front-end, use whatever tools you like with whatever HTML you wish 
to craft. The project is intended to keep the front-end build process simple using the core technologies of HTML and 
CSS.
* **Layered architecture** - The project delivers content via a solid foundation of HTML. Dynamic functionality can be 
layered on either via server-side PHP or JavaScript.
* **Accessible, performant front-end** - It's important the front-end is accessible and available to all. All content 
should be accessible without JavaScript. Performance is an important part of making websites available and this should 
be considered when developing new features.  
* **Stateless & scalable** - We build sites for clients which have high-traffic and multiple webservers. Therefore the 
front-end is designed to be stateless and should be able to run on one or many servers. This means any data stored 
locally should not be persistent and can be re-generated easily. Page content is intended to be cached, so by default 
is the same for all users.
* **Support any data source** - Although we started with WordPress, the aim is to support reading content from any data 
source. At present we are focussed on using REST APIs.  
* **Focussed on common problems** - The 80/20 rule, the project is intended to have tools to solve common front-end 
build problems and allow for customisation for everything else. At present this project is driven by the requirements 
of digital agency Studio 24, though we are happy to consider any new feature if it fits a common use case.

## History 

The project was created in December 2018 by [Studio 24](https://www.studio24.net/) to help create websites based on a 
[Headless CMS architecture](https://www.studio24.net/blog/what-is-a-headless-cms/), initially on the 
[WordPress](https://wordpress.org/) platform. 

Headless gives a lot of flexibility since you can develop the front-end in any way you wish. More often that not, the 
industry uses a JavaScript focussed solution with Headless that requires large payloads of JavaScript to render content 
based pages. We experienced this ourselves when taking on a new client with a poor performing JS based site.

This isn't how Studio 24 believes the web should be built. We have a fantastic technology for content-driven pages, 
HTML. JavaScript is brilliant and can be used with 
[progressive enhancement](https://www.gov.uk/service-manual/technology/using-progressive-enhancement) to add 
interactivity to pages.  

Static site generators offer a performant alternative to JAM Stack. However, they lack any dynamic features such as 
search. 

Studio 24 decided to develop a PHP-based application built on top of [Symfony](https://symfony.com/) that generates a 
front-end site using data from APIs. We've built tools to try to overcome some of the difficulties of Headless. Caching 
is built-in to help performance. 

We've open sourced this project to both ensure our public sector clients can use it and to share it with the community. 
We hope you find it useful! 

   

