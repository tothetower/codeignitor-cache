codeignitor-cache
=================

codeignitor cache :
Cache several parts of a page and combine them into one cache file

Example 1

Assuming you have 2 views header.php, footer.php, some data from a model and some dynamic data that can't be cached.
Do this in your controler:

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

cachepart1 contains header and dataFromModel
dynamicData contains items that can't be cached (realtime data)
cachepart2 contains the footer

-----

Example 2


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
 
$cachepart1 = nameofview1 . nameofview2 . data1
$cachepart2 = data2 . data3
$cachepart3 = nameofview3


