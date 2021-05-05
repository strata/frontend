# Design principles

New features should be reviewed against our design principles:

* **Simplify front-end development** - Freedom on the front-end, use whatever tools you like with whatever HTML you wish 
to craft. The project is intended to keep the front-end build process simple using the core technologies of HTML and 
CSS.
* **Layered architecture** - The project delivers content via a solid foundation of HTML. Dynamic functionality can be 
layered on either via server-side PHP or JavaScript.
* **Accessible, performant front-end** - It's important the front-end is accessible and available to all. Core page content 
must be delivered as server-rendered HTML. Performance is an important part of making websites available and this should 
be considered when developing new features.  
* **Stateless & scalable** - We build sites for clients which have high-traffic and multiple webservers. Therefore, the 
front-end is designed to be stateless and should be able to run on one or many webservers. This means any data stored 
locally should not be persistent and can be re-generated easily. Core page content is considered the same for all users, 
and is intended to be cached via full-page caching.
* **Support any data source** - Although we started with WordPress, the aim is to support reading content from any data 
source. At present we are focussed on supporting REST APIs and GraphQL.
* **Focussed on common problems** - The 80/20 rule, the project is intended to have tools to solve common front-end 
build problems and allow for customisation for everything else. At present this project is driven by the requirements 
of digital agency Studio 24, though we are happy to consider any new feature if it fits a common use case.

   

