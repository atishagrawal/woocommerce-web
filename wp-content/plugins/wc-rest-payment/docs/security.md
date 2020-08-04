# Security

Payment is very sensitive stuff, it is vital for business. Please please please make sure you have security implementation. 

In this guide, we'll share some good security measures for your Website and REST API.

## WordPress Security

### HTTPS & SSL

Let's face it. The internet is moving towards HTTPS. So should your website.

For more information, please [read this guide](https://www.briancoords.com/tech/enabling-ssl-https-wordpress/). 

### .htaccess & Limited Login Attempts

Having .htaccess & Limited Login Attemps can protect your website against various attacks, such as DDOS, Brute Force, and more.

Our team uses BPS Security for this purpose. For more information, please [visit BPS Security](https://wordpress.org/plugins/bulletproof-security/). 

### Firewall & Virus Scan

Regularly scan your website for vulnarabilities is a good thing. 

Our team uses Wordfence for this purpose. For more information, please [visit Wordfence](https://www.wordfence.com/). 

## REST API Security

### Authentication

In additional to HTTPS & SSL, you should also consider authenticating your REST API Requests. 

Authentication tells you exactly **Who is doing what**. In sensitive activities, this can be extremely useful.

For more information, please [see Authentication](./authentication).