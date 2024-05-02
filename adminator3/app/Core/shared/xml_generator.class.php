<?php
class c_xml_generator
{
    var $data = array(
    0=>array('type'=>'root', 'parent'=>-1)
    );
    var $auto_indent = '  ';
    var $append_after_element = "\n";
    var $xml_version = '1.0';
    var $xml_encoding = 'iso-8859-2';

    function add_node($parent, $name, $params=array())
    {
        $new_id = $this->_get_new_id();
        $this->data[$new_id] = array(
        'type' => 'node',
        'parent' => $parent,
        'params' => $params,
        'name' => $name
        );
        return($new_id);
    }
  
    function add_node_cdata($parent, $name, $data, $params=array())
    {
        $new_id = $this->_get_new_id();
        $this->data[$new_id] = array(
        'type' => 'node_cdata',
        'parent' => $parent,
        'params' => $params,
        'name' => $name,
        'data' => $data
        );
        return($new_id);
    }

    function add_cdata($parent, $data)
    {
        $new_id = $this->_get_new_id();
        $this->data[$new_id] = array(
        'type' => 'cdata',
        'parent' => $parent,
        'data' => $data
        );
        return($new_id);
    }

    function add_entity($parent, $name)
    {
        $new_id = $this->_get_new_id();
        $this->data[$new_id] = array(
        'type' => 'entity',
        'parent' => $parent,
        'name' => $name
        );
        return($new_id);
    }

    function add_note($parent, $text)
    {
        $new_id = $this->_get_new_id();
        $this->data[$new_id] = array(
        'type' => 'note',
        'parent' => $parent,
        'text' => $text
        );
        return($new_id);
    }

    function create_xml($start_node=0)
    {
        return($this->_create_xml_node($start_node));
    }

    function _get_new_id()
    {
        if (count($this->data) > 0) {
            return(array_reduce(array_keys($this->data), 'max') + 1);
        } else {
            return(1);
        }
    }
  
    function _create_xml_node($id)
    {
        static $level = 0;
    
        $level++;
        $node = &$this->data[$id];
        switch($node['type']) {
        case 'node' :
            $ret = '<'.$node['name'];
            foreach($node['params'] as $param_name => $param_value) {
                $ret .= ' '.$this->_encode_param_name($param_name).
                '="'.$this->_encode_param_value($param_value).'"';
            };
            $childs = '';
            $complete_tag = true;
            foreach($this->data as $node_id => $node_data) {
                if ($node_data['parent'] == $id) {
                      $complete_tag = false;
                      $childs .= $this->_create_xml_node($node_id);
                }
            };
            if ($complete_tag) {
                $ret .= ' />';
            }
            else {
        
                if(preg_match("/<|>/", $childs) == 0 ) {
                    $ret .= '>'.$childs.'</'.$node['name'].'>';
                }
                else
                {
                    $ret .= '>'.$this->append_after_element.$childs.str_repeat($this->auto_indent, $level - 1).'</'.$node['name'].'>';
                }
            }
            
            break;
        case 'node_cdata' :
            $ret = '<'.$node['name'];
            foreach($node['params'] as $param_name => $param_value) {
                $ret .= ' '.$this->_encode_param_name($param_name).
                '="'.$this->_encode_param_value($param_value).'"';
            };
            $ret .= '>'.$this->_encode_cdata($node['data']).'</'.$node['name'].'>';
            break;
        case 'cdata' :
            $ret = $this->_encode_cdata($node['data']);
            break;
        case 'entity' :
            $ret = '&'.$node['name'].';';
            break;
        case 'note' :
            $ret = '<!--'.$this->_encode_cdata($node['text']).'-->';
            break;
        case 'root' :
            $ret = '<'.'?xml'.
            ($this->xml_version ? ' version="'.$this->xml_version.'"' : '').
            ($this->xml_encoding ? ' encoding="'.$this->xml_encoding.'"' : '').
            '?'.">\n";
            foreach($this->data as $node_id => $node_data) {
                if ($node_data['parent'] == 0) {
                    $complete_tag = false;
                    $ret .= $this->_create_xml_node($node_id);
                }
            };
            break;
        };
        $level--;
    
        if($node['type'] == "cdata") {
    
        }
        else
        {
    
            $ret = str_repeat($this->auto_indent, $level).
             $ret.
             $this->append_after_element;
        }
    
        return($ret);
    }
  
    function _encode_param_name($text)
    {
        return(htmlspecialchars($text));
    }
  
    function _encode_param_value($text)
    {
        return(str_replace(
            array("\n","\r"),
            array('&x0A;','&x0D'),
            htmlspecialchars($text)
        ));
    }

    function _encode_cdata($text)
    {
        if (is_array($text)) { return('Chyba: nelze pouzit array jako CDATA');
        }
        return(htmlspecialchars($text));
    }

}
?>
