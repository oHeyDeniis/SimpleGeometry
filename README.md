# SimpleGeometry
A simple way to apply geometry to your entities
# How to use

Place your geometry files in the SimpleGeometry plugin's folder

Put the image and the json with the same names

example: Test.png and Test.json

after that use /ag list to see if they are correctly included

Enter the .json file and identify the name of your geometry, example:

```php
{
	"format_version": "1.10.0",
	"geometry.test": {
		"texturewidth": 64,
		"textureheight": 64,
		"visible_bounds_width": 4,
		"visible_bounds_height": 4,
		"visible_bounds_offset": [0, 1, 0],
		"bones": [
			{
				"name": "bone",
				"pivot": [-1, 0, 0],
				"cubes": [
					{"origin": [-17, 35, 0], "size": [1, 1, 1], "uv": [60, 0]}.....

```
In this case the name of this geometry is `geometry.test` but yours can be different

Save this name, after entering the game use `/ag [my|entity] [geometryName] [geometryFile]`

`[my|entity]` my to put the geometry into yourself and entity to select an entity to put the geometry into.

`[GeometryName]` the previously saved name, in this case *geometry.test*.

`[geometryFile]` the name of the png and .json file with no extension, just *Test* and not *Test.json*.


after that if you have selected entity just click on one to change it, if you have chosen my it will already change your geometry.

if you want to cancel use `/ag cancel`

