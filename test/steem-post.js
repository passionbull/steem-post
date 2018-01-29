
function test() {
   alert( 'Hello, world!' );
}

function postArticleIndetails(token, author, main_tag, title, content, tag_array )
{
  steem.api.setOptions({ url: 'https://api.steemit.com'});
  var permlink = new Date().toISOString().replace(/[^a-zA-Z0-9]+/g, '').toLowerCase();
  steem.broadcast.comment(token, 
     '',
     main_tag,// main tag
     author,
     permlink+'-post',
     title, 
     content, 
     {tags: tag_array,app: 'test'}, 
     function(err, result) {
        console.log(err, result);
    });
}

function postArticle()
{
  steem.api.setOptions({ url: 'https://api.steemit.com'});

  var token = '5JLtZpULiMgRA792itR7beCpFAEubfjjrV2X3YzXzRFCFmDBLd3';
  var author = 'passionbull' 
  var permlink = new Date().toISOString().replace(/[^a-zA-Z0-9]+/g, '').toLowerCase();

  steem.broadcast.comment(token, 
     '',
     'test',// main tag
     'passionbull',
     permlink+'-post',
     'This is the title', 
     'This is the body', 
     {tags: ['test'],app: 'test'}, 
     function(err, result) {
        console.log(err, result);
    });
}
