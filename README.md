# Bright Cloud Studio's Add User Fields
## Adds two custom user fields, "User Image" and "User Bio", which can be added to templates using custom tags. Add into news templates with a dynamic id to display an author's image and bio on posts or put in a static id to display a specific user's image and/or bio anywhere you'd like.

- On the "Edit User" page is a new section containing our two custom fields.

![Example Image 1](https://raw.githubusercontent.com/bright-cloud-studio/add-user-fields/main/images/ss_1.png)

- On any template, for this example I'm using "news_full.html5", add the custom tags "{{user_image::id}}" and "{{user_bio::id}}" to display them, making sure to replace id with the User's id.

![Example Image 2](https://raw.githubusercontent.com/bright-cloud-studio/add-user-fields/main/images/ss_2.png)

> For this example we wanted to add an author's image and bio to news posts so $author_id is obtained using \NewsModel::findByAlias but you can use them anywhere as long as you add a valid user id

- Can be styled using the "user_image" and "user_bio" class and id.
