<?php
/* 
* Cacheall Class
* Stacked partial caching for CodeIgnitor
* Stores views and data in 1 cachefile

*
* The library has 4 methods:
* 1 $this->cacheall->read()          reads the cache 
* 2 $this->cacheall->write()         writes the cache 
* 3 $this->cacheall->delete()        deletes a specific cachefile: 
* 4 $this->cacheall->delete_all()    deletes all cachefiles 
*
* The read method takes 1 argument an unique name for the cachefile 
* $cachefilename = 'myHomePage';
*
* The write function of the library takes 2 arguments:
* An unique name for the cachefile  
* $cachefilename = 'myHomePage';
*
* An array with parts to cache this array can hold strings (views or a seperator = '||||') 
* or an array (data from functions, queries, models or a seperator = '||||' )                  
*
*
* Assuming you have 2 views header.php, footer.php, some data from a model and some dynamic data that can't be cached.
* Do this in your controler:
*/
$cachefilename = 'myHomePage';
if ( ! $cache = $this->cacheall->read($cachefilename)
{ 
     $cachefiles = array(
                       header, 
                       array(
                           $dataFromModel
                       ),
                       "||||",
                       footer
                   );
     $this->load->model('modelname');
     $dataFromModel = $this->modelname->get_something();
     $cache = $this->cacheall->write($cachefiles, $cachefilename);
}
list($cachepart1, $cachepart2) = explode('||||', $cache);
$output = $cachepart1 . $dynamicData . $cachepart2;
$this->output->set_output($output); 

/*
cachepart1 contains header and dataFromModel
dynamicData contains items that can't be cached (realtime data)
cachepart2 contains the footer

A more complex example here you need 3 views and 3 data parts

Do this in your controler:
*/
$cachefilename = 'myHomePage';

if ( ! $cache = $this->cacheall->read(cachefilename)
{ 
     $this->load->model('modelname');
     $data1 = $this->modelname->get_something();
     $data2 = $this->function->calculate_something();
     $data3 = $this->modelname->get_some_other_thing();
     $cachefiles = array(
                 "nameofview1",
                 "nameofview2",
                 array(
                     $data1,
                     "||||",
                     $data2,
                     $data3
                 ),
                 "||||",
                 "nameofview3"
              );
     $cache = $this->cacheall->write($cachefiles, $cachefilename);
}
list($cachepart1, $cachepart2, $cachepart3) = explode('||||', $cache);
$output = $cachepart1 . $dynamicData . $cachepart2 . $moreDynamicData . cachepart3;
$this->output->set_output($output);
/* 
$cachepart1 = nameofview1 . nameofview2 . data1
$cachepart2 = data2 . data3
$cachepart3 = nameofview3
 
The delete function takes one argument: an unique name for the cachefile to delete


The delete_all function has no arguments it deletes all cachefiles in the directory.
If the specified directory is the same as the CI cache directory those cachefiles are deleted too.


 INSTALLATION: 

 # Load the library
 Add the next line to /application/config/autoload.php
 $autoload['libraries'] = array('cacheall');

 # Configure the library
 Add the next 4 lines to application/config/config.php
 $config['library_cacheall_expiration'] = 86400;                // in seconds 60*60*24 = 86400 = 1 day
 $config['library_cacheall_path'] = '/application/cache/';      // your path to CI cache --> should be writeable
 $config['library_cacheall_seperator'] = '||||';                // the seperator you want
 $config['library_cacheall_serialize'] = 0;                     // 1 = serialize the cache     0 = don't serialize the cache

 # Put the library in place
 Copy the library to application/libraries


@link        http://www.happyshop.nl/index.php/home/cicaching
*/


