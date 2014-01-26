<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Areas
 *
 * @author Harry
 */
class Areas {
    
    public function getAllAreas(){
        // returns all the areas available. 
        // This returns Area objects, which inturn contain 
        // each of the Regions and the Counties
        // We an actually use a sort of waterfall type thing
        // Where we call the other models to populate this stuff, suchas as get all regions, whcih calls get all Areas.
    }
    
    public function addNewArea($name){
        //Adds a new area. - includes the actual xml access
        // return boolean yay or nay. 
    }
    
    public function UpdateArea($name){
        // Updates an Area- the actual xml. 
        // returns a boolean for yay or nay
    }
    
    public function
    
}
