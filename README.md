# DP Maintenance Mode

DP Maintenance Mode is a versatile WordPress plugin designed to simplify the process of enabling maintenance mode on your website. With its intuitive interface, users can swiftly activate maintenance mode, ensuring a seamless transition when performing updates or making site adjustments. Additionally, DP Maintenance Mode offers a highly customizable Construction Mode, ideal for extended periods of site downtime. This feature-rich plugin includes a WYSIWYG editor, empowering users to craft engaging maintenance pages effortlessly. Furthermore, it incorporates social media icons, facilitating easy communication with visitors even during maintenance. With built-in role capabilities, administrators can fine-tune access permissions, ensuring a smooth maintenance experience for both users and site administrators.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Contributing](#contributing)
- [Todo](#todo)
- [License](#license)

## Installation

1. Download the zip file using the Code button above
2. Upload `/dp-maintenance-mode/` to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Navigate to Settings -> Maintenance Mode

## Usage

1. **Enable Maintenance Mode:**
   - Check the "Enabled" box in the plugin settings to activate maintenance mode.

2. **Select Mode:**
   - Choose the desired mode from the available options (e.g., Maintenance Mode, Construction Mode) based on your requirements.

3. **Add Content:**
   - Utilise the WYSIWYG editor to create and customise your mixed media content. This content will be displayed to visitors while the site is under maintenance.

4. **Construction Mode Customization (Optional):**
   - If using Construction Mode:
     - **Add Logo:** Upload a logo image to be displayed in the header section.
     - **Add Social Media Profiles:** Provide links to your social media profiles to be displayed on the maintenance page.
     - **Add Custom Button:** To add a button, assign the `btn` class to an anchor (`<a>`) tag within your content. This can be done directly in the WYSIWYG editor.

5. **Preview:**
   - Once configured, click the preview button to ensure everything appears as intended.

6. **Save Settings and Activate:**
   - Finally, activate maintenance mode by clicking the "Save" button to make the page live and restrict access to your site while you perform maintenance tasks or create your website.

7. **Deactivate Maintenance Mode:**
   - To disable maintenance mode and restore normal site functionality, simply uncheck the "Enabled" box in the plugin settings.

8. **Additional Customization (Optional):**
   - To enable custom styling, simply include a file named `maintenance.css` in your theme folder. Once detected by DP Maintenance Mode, its usage will be reflected in this section.

## Features

- Simplicity: Crafted with a focus on simplicity, ensuring user-friendliness and ease of use.
- Customization: Enjoy the full power of WYSIWYG customization, allowing for the addition of images, media, links, shortcodes, and more. The Text/Code tab is also available for those preferring custom markup.
- Mobile Compatibility: Thanks to its responsive design, DP Maintenance Mode seamlessly adapts to mobile devices, providing an optimal user experience on smartphones and tablets.
- Role Management: Utilise user role control functionality to tailor access permissions.
- Custom Styling: Enhance visual appeal by optionally integrating a custom stylesheet.
- Code Snippets: Enhance functionality with the option to seamlessly insert code snippets onto the page.

## Contributing

1. Fork the repository
2. Create a new branch (`git checkout -b feature-branch`)
3. Make your changes
4. Commit your changes (`git commit -am 'Add new feature'`)
5. Push to the branch (`git push origin feature-branch`)
6. Submit a pull request

## Todo

- [ ] Improve settings page layout
- [ ] Replace Font Awesome with local SVGs for social media icons
- [ ] Add expanded customisation options for Construction Mode
- [ ] Ability to add a links menu in header for Construction Mode
- [ ] Add ability to choose location for code snippet in construction mode (header, footer etc.)
- [ ] Internationalization

## License

This project is licensed under the GNU General Public License v3.0 - see the [LICENSE.md](LICENSE.md) file for details.