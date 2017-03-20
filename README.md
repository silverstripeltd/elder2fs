# Elder2fs

This tool connects to Elder and renders KB articles into regular md files.

## Installation

	composer require "silverstripe-platform/elder2fs"

## Usage

In your project root, create `elder2fs.yml`. Here is an example:

	elderUrl: <elder URL>
	tokens:
	  replacement:
	    token:
	      companyName: Terrible Ideas Ltd.
	pages:
	  stuff:
	    manual:
	      kb: kb003456
	      version: 1.7
	      locale: en_NZ
        installation:
          kb: kb000224

`elderUrl` is the address at which Elder is running. It's specific to your infrastructure.

`tokens` must contain all replacement tokens required by the KBs being rendered, otherwise Elder API calls will fail.

`pages` is the output tree of directories and Markdown files. Missing directories will be created.

Once configured, you can run elder2fs by:

	vendor/bin/elder2fs
