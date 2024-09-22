    function I18n(locale) {
        
        this._locale = locale;
        
    }
    
    I18n.prototype.setLocale = function(locale){
        
        this._locale = locale;
        
    }
    
    I18n.prototype.setDictionary = function(dictionary){
        
        this._dictionary = dictionary;
        
    }
    
    I18n.prototype.get = function(template, values) {
        
        if (this._dictionary == null) {
            
            return template;
            
        }
        
        if (this._dictionary[template] != null) {
            
            template = this._dictionary[template];
            
        }
        let currentIndex = 0;
        return template.replace(/%[ds]/g, (match) => {
            
            if (values != null && currentIndex < values.length) {
                
                const replacement = values[currentIndex];
                currentIndex++;
                return replacement;
                
            }
            
            return match;
            
        });
    };
    
