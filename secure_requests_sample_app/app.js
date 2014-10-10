(function() {

  return {

    requests: {
      getFromTeachmyapi: {
        url: 'https://www.teachmyapi.com/api/{{setting.key}}/users',
        type: 'GET',
        dataType: 'json',
        secure: true,
        headers: {
          'X-Setting': '{{setting.key}}'
        }
      },

      postToHerokuapp: {
        url: 'http://{{setting.subdomain}}.herokuapp.com/200/OK/',
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({a: '{{setting.new_data}}'}),
        secure: true
      }
    },

    events: {
      'app.activated'           : 'init',
      'click .fetch'            : 'getInfo',
      'getFromTeachmyapi.done'  : 'renderInfo',
      'getFromTeachmyapi.fail'  : 'fail',
      'click .back_to_start'    : 'renderStartPage',
      'click .post'             : 'postInfo',
      'postToHerokuapp.fail'    : 'fail',
      'postToHerokuapp.done'    : 'render'
    },

    init: function() {
      this.switchTo('start_page');
    },

    getInfo: function(event) {
      event.preventDefault();
      this.ajax('getFromTeachmyapi');
      this.switchTo('loading');
    },

    renderInfo: function(data) {
      var users = _.map(data, function(user) {
        user.friends = user.friends.join('; ');
        return { user: user };
      });
      var userPageObj = { users: users };
      this.switchTo('list', userPageObj);
    },

    postInfo: function(event) {
      event.preventDefault();
      this.ajax('postToHerokuapp');
      this.switchTo('loading');
    },

    render: function(data) {
      var responses = _.map(data, function(val, key) {
        return {
          key: key,
          val: val
        };
      });
      this.switchTo('success', {responses: responses});
    },

    fail: function(data) {
      services.notify(JSON.stringify(data));
      this.switchTo('start_page');
    },

    renderStartPage: function() {
      this.switchTo('start_page');
    }
  };

}());
