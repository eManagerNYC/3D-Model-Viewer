3D-Model-Viewer
===============

vA3C model viewer for wordpress.

## License

Software is free to use/hack/fork and is license under GPLv2 or higher.  Please share any significant contrbutions to the community. 

## Installation

1.  Download zip from [repository](https://github.com/TurnereManager/3D-Model-Viewer)
2.  In your `/wp-admin/` visit plugins > add new
3.  Click upload
4.  Upload zip and activate

## Use

Two Shortcodes

1.  `[model]` (recommended)

	- `url` path to .js file
	- `width`
	- `height`

	For example: `[model url="mypath/to/file.js"]`

2.  `[va3c]`

	- `type` can be object or iframe, default is object
	- `width`
	- `height`

## Exporting

To create a `.js` from a Revit (or other) model use the [vA3C plugin](http://va3c.github.io/) at [https://github.com/va3c/](https://github.com/va3c/)
