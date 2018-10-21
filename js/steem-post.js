jQuery(document).ready(function($) {  

    if( wpsePost.Message == 6 ){
        alert('Post published');
    }else if( wpsePost.Message == 1 ){
        alert('Post updated');
    }

    var tag_array = wpsePost.Tags.split(',');
    steem.api.setOptions({ url: 'https://api.steemit.com'});
    //var permlink = new Date().toISOString().replace(/[^a-zA-Z0-9]+/g, '').toLowerCase();
    var permlink = wpsePost.Post_ID+'-warp-'+tag_array[0];
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
    var commentOptions = {
        'author': wpsePost.ID, 
        'permlink': permlink, 
        'max_accepted_payout': '1000000.000 SBD',
        'percent_steem_dollars': 10000,
        'allow_votes': true,
        'allow_curation_rewards': true,             
        'extensions': [
            [0, {
                'beneficiaries': [{
                    'account': 'jacobyu',
                    'weight': 500
                }]
            }]
        ]
    };

    if( wpsePost.Message == 6 ){
    operations.push(['comment_options', commentOptions]);
    operations.push(['vote', { voter: wpsePost.ID, 'author': wpsePost.ID, 'permlink': permlink, 'weight': 10000 }]);

    }else if( wpsePost.Message == 1 ){
    }

    steem.broadcast.sendAsync(
        { operations, extensions: [] },
        { posting: wpsePost.Token }
      );
});