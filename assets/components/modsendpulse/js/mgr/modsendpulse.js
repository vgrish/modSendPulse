var modsendpulse = function (config) {
	config = config || {};
	modsendpulse.superclass.constructor.call(this, config);
};
Ext.extend(modsendpulse, Ext.Component, {
	page: {}, window: {}, grid: {}, tree: {}, panel: {}, combo: {}, config: {}, view: {}, utils: {}
});
Ext.reg('modsendpulse', modsendpulse);

modsendpulse = new modsendpulse();