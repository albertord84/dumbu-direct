function Profile(pk, profPic, username, fullName, byline)
{
  this.pk = 'undefined' == typeof pk ? null : pk;
  this.profPic = 'undefined' == typeof profPic ? null : profPic;
  this.username = 'undefined' == typeof username ? null : username;
  this.fullName = 'undefined' == typeof fullName ? null : fullName;
  this.byline = 'undefined' == typeof byline ? null : byline;
};
