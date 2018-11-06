# TYPO3 Extension `site_management`

This extension makes it possible to duplicate a page tree including connected backend user, groups and file mounts.

Don't use the extension in production yet!

**Requirements**

- TYPO3 CMS 9.5

## Manual

### Setup

1. Install extension. Use `composer require georgringer/site-management:dev-master`.
2. Create a demo trees.
3. Mark the root page of a demo tree as demo tree in the page properties.
4. Create backend users, backend groups and filemounts and select the demo tree in each record.

### Usage backend module

1. Switch to the *Site management* module
2. Select a demo tree which should be copied
3. Fill in the data and the tree will be created

## Development

UnitTests can be called with the following command:

```
./phpunit -c ./vendor/typo3/testing-framework/Resources/Core/Build/UnitTests.xml web/typo3conf/ext/site_management/Tests
```

