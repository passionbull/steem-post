jQuery(document).ready(function($) {  

    if( wpsePost.Message == 6 ){
        alert('Post published');

    }else if( wpsePost.Message == 1 ){
        alert('Post updated');
    }
    
    console.log(wpsePost.ID);
    console.log(wpsePost.Token);
    console.log(wpsePost.Tags);
    console.log(wpsePost.Title);
    console.log(wpsePost.Content);
    
    // var tag_array = wpsePost.Tags.split(',');
    // steem.api.setOptions({ url: 'https://api.steemit.com'});
    // //var permlink = new Date().toISOString().replace(/[^a-zA-Z0-9]+/g, '').toLowerCase();
    // var permlink = wpsePost.Post_ID+'-hj-'+tag_array[0];
    // permlink = permlink.toLowerCase();
    
    // steem.broadcast.comment(wpsePost.Token, 
    //    '',
    //    tag_array[0],// main tag
    //    wpsePost.ID,
    //    permlink,
    //    wpsePost.Title, 
    //    wpsePost.Content, 
    //    {tags: tag_array,app: 'test'}, 
    //    function(err, result) {
    //       console.log(err, result);
    //   });    
});