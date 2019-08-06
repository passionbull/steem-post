String.prototype.replaceAll = function(org, dest) {
    return this.split(org).join(dest);
}

jQuery(document).ready(function($) {  

    if( wpsePost.Message == 6 ){
        alert('Post published');
    }else if( wpsePost.Message == 1 ){
        alert('Post updated');
    }

    var tag_array = wpsePost.Tags.split(',');
    steem.api.setOptions({ url: 'https://api.steemit.com'});
    //var permlink = new Date().toISOString().replace(/[^a-zA-Z0-9]+/g, '').toLowerCase();
    
    var permlink_end = wpsePost.Slug;
    // var pattern = /[\u3131-\u314e|\u314f-\u3163|\uac00-\ud7a3]/g;
    // var isKR = (pattern.test(decodeURIComponent(wpsePost.Slug))) ? true : false;
    // if(isKR){
    //     permlink_end = 'warp-'.tag_array[0];
    // }
    var permlink = wpsePost.Post_ID+'-'+permlink_end;
    permlink = encodeURI(permlink);
    permlink = permlink.toLowerCase();

    var operations = [['comment', {
            'parent_author': '', 
            'parent_permlink': tag_array[0], 
            'author': wpsePost.ID, 
            'permlink': permlink, 
            'title': wpsePost.Title, 
            'body': wpsePost.Content, 
            'json_metadata': JSON.stringify({ 
                     tags: tag_array, 
                     app: 'Steem.js' 
                   })
        }]];

    operations.push(['vote', { voter: wpsePost.ID, 'author': wpsePost.ID, 'permlink': permlink, 'weight': 10000 }]);
    steem.broadcast.sendAsync(
        { operations, extensions: [] },
        { posting: wpsePost.Token },
        function(err, result){
            console.log(err);
            console.log(result);
            if(result == undefined){
                console.log('Hello');
                steem.broadcast.sendAsync(
                    { operations, extensions: [] },
                    { posting: wpsePost.Token }
                  );
            }
        }
    );

});
