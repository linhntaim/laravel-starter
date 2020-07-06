<?php

Route::get('/{path?}', 'FileController@show')->where('path', '.*');
