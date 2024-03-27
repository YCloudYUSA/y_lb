# Welcome to Y Layout Builder smoke tests documentation

In order for Y Layout Builder dependencies being tested in a short timeframe, please follow steps below

# Main menu

## Add <nolink> URL to the main menu

### User

**Administrator**

### Steps

1. Login as admin
2. Go to Structure -> Menus (/admin/structure/menu)
3. Verify you see the **Main navigation** menu
3. Click the **Edit Menu** for the Main navigation (/admin/structure/menu/manage/main)
4. Add a new one or edit an existing first level item of the menu
5. Verify you can add in the **Link** field  a `<nolink>` value and submit the form
6. Add a new one or edit the second level item of the menu
7. Repeat step 5 for the menu item
8. Go to home page
9. Verify the menu looks and works for items with real and <nolink> URL

### Expected Results
1. If i set a first level menu item with <nolink> it is styled properly and opens the child menu
2. If i set a second level menu item with <nolink> it is styled properly and opens the child menu
3. If i set a first level menu item with <nolink> it is styled properly
