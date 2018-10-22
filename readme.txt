=== WarpSteem ===
Plugin Name: WarpSteem
Author: jacobyu
Author URI: https://busy.org/@jacobyu
Tags: steemit, steem, warpsteem
Requires at least: 4.8.4
Tested up to: 4.9.2
Stable tag: 1.0.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

When you write a post on wordpress, the post is updated to steemit.
Also, you can send all posts you wrote to stemmit for marketing and side-money. 

== Description ==
WarpSteem is a wordpress plugin that connect wordpress to steemit.

Steemit (https://steemit.com/) is a blog platform that is based on blockchain.

You can easily write something on steemit using this plugin.

For using this, you need to have steemit acount.

You can get an account at https://signup.steemit.com/.

If you have question, you can contact me anytime using wordpress or steemit.

== Installation ==
You go to setting page and fill your information (user id, steemit posting key, tag).

The installation procedure is as follows.

1. setting warpsteem
    - Setting -> Warpsteem setting -> Posting settings
    - You are supposed to write ID, posting key, tag on each line.
    - You can set whether you will send wordpress post to steem or not.

- example
passionbull
test1234tokenonlypostingkey
test,wordpress

2. write your post on wordpress

3. click publish/update button

4. check your steem

== Screenshots ==
1. setting page image

2. posting key image

== Changelog ==
v1.0
- Publish your newly created post on wordpress to steemit
- Publish your old post on wordpress to steemit
- Update your steem_post if you have write wordpress post again
- Select Enable post update or not

v1.0.1
- Set your permlink (slug) and tags on editor, not setting page
- Upgrade setting page UI

== Upgrade Notice ==
- [x] Set custom post permalink for your Steem post
- [x] Set post tags for your Steem post
- [ ] Set post reward options such as Power Up (100%), Default (50% / 50%), and Decline Payout
- [ ] Easy to use User Interface

== Additional Info One ==
- We used steem.min.js which is third-pary javascript library.
- Steem.min.js is the JavaScript API for Steem blockchain (https://github.com/steemit/steem-js)
- Using steem.min.js, this plugin gets posting key of steem users. 
- Posting key has only permission for writing blog on steemit.
- This plugin requests posting key for sending wordpress post to steemit.