module.exports = function(grunt){
  grunt.initConfig({
      pkg: grunt.file.readJSON('package.json'),
      sass: {
          AppDev: {
            files: {
              'assets/stylesheets/theme.css': 'assets/stylesheets/sass/theme.scss'
            }
          }
      },
      watch: {
        files: [
          'assets/stylesheets/sass/*.scss',
          'assets/stylesheets/sass/gui/*.scss',
          'assets/stylesheets/sass/config/*.scss',
          'assets/stylesheets/sass/base/*.scss',
          'assets/stylesheets/sass/modules/*.scss',
          'assets/stylesheets/sass/skins/*.scss',
          'assets/stylesheets/sass/vendor/*.scss',
          'assets/stylesheets/*.css',
          'assets/stylesheets/skins/*.css'],
        tasks: ['sass']
      }
  });

  grunt.loadNpmTasks('grunt-contrib-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.registerTask('dev', [ 'sass']);
  grunt.registerTask('default', [ 'sass']);
};
