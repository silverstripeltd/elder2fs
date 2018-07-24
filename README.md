# Elder2fs

This tool connects to Elder and renders KB articles into regular md files.

## Installation

	composer require "silverstripeltd/elder2fs"

## Usage

In your project root, create `elder2fs.yml`. Here is an example:

    elderUrl: <elder URL>
    variables:
      platform:
        companyName: Terrible Ideas Ltd.
    pages:
      stuff:
        index.md:
          url: https://github.com/silverstripe/silverstripe-framework/blob/4/docs/en/index.md
        installation.md:
          url: https://github.com/silverstripe/silverstripe-framework/tree/4/docs/en/00_Getting_Started/01_Installation/index.md

`elderUrl` is the address at which Elder is running. It's specific to your infrastructure.

`variables` must contain all variables required by the KBs being rendered, otherwise Elder API calls will fail.

`pages` is the output tree of directories and Markdown files. Missing directories will be created.

Once configured, you can run elder2fs by:

	vendor/bin/elder2fs

If it's human operated, you might want to ensure caches are flushed, especially if you have just committed some changes:

	vendor/bin/elder2fs --flush
