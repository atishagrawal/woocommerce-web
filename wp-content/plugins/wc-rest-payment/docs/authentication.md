# Authentication

WordPress or not, it is often a good idea for front-end to **authenticate the REST API Request**, so the back-end knows who is making the request, and decide whether the request should be accepted or rejected.

While you can still process payment without authentication using WC REST Payment, we **strongly recommend authenticating the client for ALL REST API Calls**. You can use any **one** of below authentication method.

Let's get started!

## Cookie Authentication

Cookie authentication is the standard authentication method included with WordPress. When you log in to your dashboard, this sets up the cookies correctly for you, so plugin and theme developers need only to have a logged-in user.

You can read more about [Cookie Authentication here](https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/#cookie-authentication).

## Basic Authentication

Basic authentication refers to the basic type of HTTP authentication in which login credentials are sent along with the headers of the request.

You can read more about [Basic Authentication here](https://www.cloudways.com/blog/setup-basic-authentication-in-wordpress-rest-api/).

**Note:** Basic Authentication transmits username and password in the request header, which could be a major **insecure issue**. Use with extreme caution!

If unsure, use [JWT Authentication](#jwt-authentication) method below.

## OAuth 1.0a & OAuth 2

OAuth is an open standard for access delegation, commonly used as a way for Internet users to grant websites or applications access to their information on other websites but without giving them the passwords.

You can read more about WordPress OAuth implementations here.

1.  [OAuth 1.0a Authentication here](https://wordpress.org/plugins/rest-api-oauth1/)
2.  [OAuth 2 Authentication here](https://github.com/WP-API/OAuth2).

## Application Passwords

With Application Passwords you are able to authenticate a user without providing that user’s password directly, instead you will use a base64 encoded string of their username and a new application password.

You can read more about [Application Passwords here](https://wordpress.org/plugins/application-passwords/).

## JWT Authentication

**J**SON **W**eb **T**okens are an open, industry standard RFC 7519 method for representing claims securely between two parties.

You can read more about [JWT Authentication here](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/).

# Summary

While technically you can process payment without Authentication, it is highly recommended that you do.

Our team exclusively use [**JWT Authentication**](#jwt-authentication) due to its **Simplicity**, Flexibility and Security.

# Next Step

That's not too hard is it? Let's move on!

Next up: [Quick Start](./#quick-start).
