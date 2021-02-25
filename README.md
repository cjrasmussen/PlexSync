# Plex Library Sync Tool

Tool for syncing library metadata for items shared across multiple libraries.

I'm the first to admit that I don't know much about the inner workings of Plex, so this may be a horrible way of doing things. It might not even work all the time. I don't know. Caveat utilitor.

## Use Case

I've got my movies split across multiple libraries.  Documentaries, kid-friendly stuff, holiday movies, and everything else. It's split that way because I don't always want to see the documentaries, or the kids stuff.  Sometimes I do want it all, though, so I have another "Movies" library that includes the items from all of the other movie libraries.

That works fine until I manually edit the metadata for a movie.  I want "Die Hard With a Vengance" to be sorted as "Die Hard 3" for example. With that movie in multiple libraries, I'd have to edit the sort title in each library. Instead, I use this tool to sync metadata from one "source" library to other libraries. That way I can edit a single library and see those changes pushed out to other libraries as appropriate. The drawback is that if I ever change something in a "child" library, it'll be overwritten by the metadata from the source library.

## Configuration

Update the `$map` array in `src/sync.php` such that the keys are either the IDs or names of your "source" library. The corresponding value for that source should be the name or IDs of each "child" library. Using an ID here is technically better for performance as it allows for skipping the ID lookup but library titles are more readable. IDs should be used if your library titles are not unique.

Update the `$fields` array in `src/sync.php` such that it includes all of the fields you want to sync. If you want to sync the title but not the sort title, remove `sort_title` from the array, for example.

## Running the Tool

Copy the `src` directory to the machine you're running Plex from. Make sure you can execute PHP on that machine. From wherever you copied `src` to, run `php sync.php`.

Depending on your setup, you might need to run it as a different user or with different permissions. On my personal setup, I need to sudo it, for example.

You can also run it as a cron/scheduled task if you know what you're doing. That's entirely up to you.

## Known Complications

Apparently the Plex database has issues with the naturalsort index getting corrupted or something, which can cause issues with this script. There's a whole thread about it [on the Plex forums](https://forums.plex.tv/t/database-corruption/236013/30).

## Author

### Clark Rasmussen

* [github.com/cjrasmussen](https://github.com/cjrasmussen)
* [twitter.com/clarkjrasmussen](https://twitter.com/clarkjrasmussen)
* [cjr.dev](https://cjr.dev)

## License

Copyright © 2020, [Clark Rasmussen](https://cjr.dev).
Released under the [MIT License](LICENSE).
