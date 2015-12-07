# dirtyjpegresizer
Quick and Dirty JPEG bulk resizer in PHP / ImageMagick

I found myself needing to resize 150,000 jpeg images in a hurry, so knocked up a quick and dirty script to do the job.

Can be used in two ways:

A) Walks directories, resizing all images, or
B) Gets image names from SQL db, works out path of each, resizes them
