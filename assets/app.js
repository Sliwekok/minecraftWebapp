// import scss
import './styles/app.scss';

// create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

require('./tooltips');
require('./main.js');
require('./watched.js');
require('./bootstrap.js');
require('./account.js');
require('./addNew');
require('./itemFetcher');
require('./search');