<!-- 
* Readme.txt
* Place this data in a wiki page and link with your calendar
* Calendar Help information
* Contact: Eric Fortin: kenyu73@gmail.com
* MediaWiki: http://www.mediawiki.org/wiki/User:Kenyu73/Calendar
-->
__TOC__
==Setup==

* It's recommended to create a custom calendar type Namespace, like '''Calendars''', but can be whatever Namespaces defined in LocalSettings.php or standard MediaWiki namespaces (like user namespaces) however, it's not required. It is recommended though so searches in the main wiki do not included calendar events. You can also force this rule by setting an optional localsetting global (see below).
* The easist way to create a new calendar page is to type "Calendars:PageName" in the search box 
* Add a <nowiki><calendar /></nowiki> extension tag to the newly create page (or existing page)
* Add parameters as required (see below listing)

Note: You can have more then one calendar per page. It's fun to find unique combinations of how to use "full" calendar view and "day" only view. Don't forget, since this is a tagged extension, you could always wrap the calendar in a table to shrink it down or justify it...


The calendar has many advanced features; below is a simple basic way to setup the calendar. This calendar will create a standard calendar named "Public" if no name parameter is given, but it's recommended that, at minimun, you create a 'name' parameter. This will give you a good all around full featured calendar. Read and use the advanced parameters at you own risk! :)
 
 <nowiki><calendar /> </nowiki>
 <nowiki><calendar name="Team Calendar" /> </nowiki>

<div style="color:red">'''Important:'''</div>
To gain the ability of Parent/Subpage linking, place the calender in an existing namespace ([[Help:Namespaces]]) or create a new one. Create a wiki page as shown above and then add your calendar extension tag to that page. This will populate a "quick" shortcut link back to the calendar after the event is entered and saved. The calendar will still work fine if not added to a namespace, but you will not get a "quick link" back to the main calender page.<br/>
[[image:namespaces example.gif]]



The following are examples of how an ('''Namespace:Page/Name/EventDate''') event will look:

 <nowiki><calendar name="Sales" /></nowiki>
     '''Calendars:Acme Company/Sales/12-1-2008 -Event 1'''

 <nowiki><calendar name="Support" /></nowiki>
     '''Calendars:Acme Company/Support/12-1-2008 -Event 1'''

=== Sharing Calendars ===
You can also "share" or subscribe to other calendars by using the "''subscribe''" or "''fullsubscribe''" parameter. This will create a calendar of your own, but you'll also have all the events listed from "Sales". Remember to use the full '''namespace:wiki page/calendar name''' format. Be sure to include ''usetemplates'' or other special parameters in your calendar if the subscibed calendar uses them.
 '''Namespace not used:'''
 <nowiki><calendar name="Support" subscribe="Acme Company/Sales" /></nowiki>

 '''Namespace is used:'''
 <nowiki><calendar name="Support" subscribe="Calendars:Acme Company/Sales" /></nowiki>

 '''To share more than one calender'''
 <nowiki><calendar name="Support" subscribe="Calendars:Acme Company/Sales, Calendars:Acme Company/Marketing" /></nowiki>

=== Parameters ===
Please use quotes for any parameter that may contain a space
{| border=1 cellpadding="5" class="prettytable"
! Parameters
! Description
! Example
! Default
! Version
|-
|'''name=<"value">'''
|Name of your calendar
|name="Family Events"
|Public
|2.0.4
|-
|'''disableaddevent'''
|The "Add Event" link is removed from the calendar.
|disableaddevent
|enabled
|2.0.4
|-
|'''yearoffset=<value>'''
|Sets the year dropdown +/- value.
|yearoffset=3
|2  (+/- years)
|2.0.4
|-
|'''date=<value>'''
|Display a single day listing. <br />Values can be: '''yesterday''', '''today''', '''tomorrow''' or a '''datevalue'''
|date=tomorrow or date=1-1-2010
|off - normal month view
|nowrap|2.0.4<br>mod: 3.7.2
|-
|'''defaultedit'''
|Whenever a user clicks an event, the event defaults to edit mode.
|defaultedit
|off - page view
|3.0
|-
|'''charlimit=<value>'''
|Sets the calendar event name max length
|charlimit=30
|20 charactors
|3.0
|-
|'''subscribe=<"value">'''
|Allows the calendar to subscribe to existing events from other calendar(s); subscribe to additional calendars delimited by a comma within the ; ''add events'' go to '''your''' calendar only
|subscribe="Main Page/Name, SomePage/Name"
|not subscribed
|3.2
|-
|'''usetemplates'''
|This allows the use of one page to add events by storing many events in one location. The templates are identifed by the button with the month name in the lower right section of the calendar.
|usetemplates
|disabled
|3.2
|-
|'''locktemplates'''
|This disables the template button and template links; template events remain visable
|locktemplates
|off
|3.2
|-
|'''fullsubscribe=<"value">'''
|Allows the calendar to subscribe impersonate another calendar; ''add events'' go to the subscribed calendar '''only'''; you can use ''subscribe'' mode if needed as well
|fullsubscribe="Tech Group/Team Calendar"
|not subscribed
|3.2
|-
|'''disablelinks'''
|This removes the ability to click/edit an existing event created via 'add event'; use 'locktemplates' to disable template created links
|disablelinks
|off - allow links/edits
|3.2
|-
|<s>usemultievent</s><br>'''usesectionevents'''
|Users clicking 'add event' opens the last entered event; you must place each event title in ==event1==, ==event2== multiple event formatting as describle later in this help.
|usesectionevents
|disabled - 'add event' will create a new event pages
|3.2
|-
|'''maxdailyevents=<value>'''
|Set the limit of how many "add event" unique pages are created; this doesn't include ''template'' or ''==event=='' type entries. This in sense forces users to use ==event1==, ==event2== formatting
|maxdailyevents=5
|5 events
|3.2
|-
|'''disablestyles'''
|Disable the 'event style' button and disables keyword search styling; inline direct syles are not effected.
|disablestyles
|enabled, but does ''nothing'' until keyword styles are added
|3.2
|-
|'''lockdown'''
|Basically puts the calendar into a read-only state; this includes 'disableaddevent', 'disablelinks' and 'locktemplates'
|lockdown
|false - no lockdown
|3.2
|-
|'''enablesummary=<value>'''
|Enables event summaries to display below the eventname; value is max character length of the summary
|enablesummary=100
|disabled
|3.4.2
|-
|'''useeventlist=<value>'''
|Enabling this displays a vertical list of all events within a defined amount of days. This hits the db alot as it must search events for every single day in the amount of future days defined... I have the code limited to 120 days, but use the least days needed.
|useeventlist=30
|disabled
|3.4.2
|-
|'''useconfigpage'''
|Use alot of parameters? Use the config page instead. Enter each parameter followed by the enter key into the config page. The ''disablelink'' option removes the btn and text links to the config page. You can use the config page and <calendar /> options together, but the <calendar /> options overwrites the ''config page'' options.
|useconfigpage<br/>useconfigpage=disablelink
|off
|3.4.2
|-
|'''css=<value>'''
|Create your own css design based off the default.css file. Rename it and load it via this parameter. I may change the default.css page during releases, so use this as long as you dont mind re-writing your custom css after an upgrade... (=
|css="olive.css"
|default.css
|3.5
|-
|'''disabletimetrack'''
|Time tracking is enabled by default and looks for double colons (::vacation-8) or (::vacation:8). This will create a dynamic listing of trackable events below the calendar.
|disabletimetrack
|enabled
|3.5
|-
|'''enablerepeatevents=<value>'''
|Repeating events are created using using (5# Vacation) within normal events. The code looks up the previous months and applies carry-over events to the current month. It may increase the calendar load time as it looks back 15 days (default) into the previous month for carry over repeating events.
|enablerepeatevents=45
|disabled
|3.5<br>mod:3.7.8
|-
|'''enablelegacy'''
|Load events from the older "Title (12-1-2008) - Event 1" format. These older events were used in some version of 3.2 and older. This may increase calendar load times as it much search for older style events and newer events. 
|enablelegacy
|disabled
|3.5.0.1<br>mod: 3.6
|-
|'''disablemodes'''
|This removes the 'year', 'month', 'week' buttons from the top of the respective pages.
|disablemodes
|enabled
|3.6
|-
|'''5dayweek'''
|This sets the 'week' mode calendar to not display weekends
|5dayweek
|full week
|3.6
|-
|'''week or year'''
|These parameters default the calendar into that requested mode
|year
|month mode
|3.6
|-
|'''ical=<value>'''
|This enables the iCalendar load tool in full calendar mode. Currently this will load 'DTSTART', 'DTEND', 'SUMMARY', 'DESCRIPTION' and most standard 'RRULE' logic. The optional value is 'overwrite'. Please see iCal readme for more info.
|ical
|disabled
|3.6
|-
|'''disablerecurrences'''
|This skips any RRULE recurrences stored in the 'page/name/recurrences' page. The page contains all imported ical repeating rules (RRULE).
|disablerecurrences
|disabled
|3.6.0.2
|-
|'''simplemonth'''
|Creates a simple month that displays only clickable numberic days. This would best be used wrapped in a <nowiki><table></nowiki> tag to create "mini-calendar" views.
|simplemonth
|normal mode
|3.7
|-
|'''monday'''
|Sets the calendar to begin on '''Monday''' (Mon-Sun)
|monday
|Sat-Sun
|3.7.4
|-
|'''disableredirects'''
|disables event redirects to other pages...ie, so you can use the page "move" without duplicating events
|disableredirects
|redirects enabled
|3.7.7
|-
|'''disablesectionevents'''
|disables the calendar from using sections (==event==) as calendar events
|disablesectionevents
|section events enabled
|3.8.2
|-
|'''style'''
|sets a default '''style''' for all events (ie: font-size, color, etc)... keyword styles via the "style" button override this default style
|style="font-size:10px; color: green; font-style:italic"
|style=""
|3.8.2
|-
|'''dayofyear'''
|displays the day of the year (1-365)
|dayofyear
|disabled
|3.8.3
|-
|'''weekofyear'''
|displays the week of the year
|weekofyear
|disabled
|3.8.3
|}

== Events ==
Events can be entered either by the "'''add event'''" link or via the "'''template load'''" button (if enabled). Both work together seemlessly, but clicking each of them will bring you back to the respective method of creation. Once you save the event, you can easily go back to the calendar via the Subpage/Parent link right above the page body. <br />

Events are listed on the calendar with the information on the first line of the page if created via "'''add event'''". <br /> 

In this example, '''Summer Picnic''' will appear on the calendar.
 Summer Picnic<br>
 Our department will be holding a summer picnic at the park.  Bring your families and your appetites!

* Note: Events can also be wiki images. Either '''<nowiki>[[Image:Picture.jpg]]</nowiki>''' or '''<nowiki>Picture.jpg</nowiki>''' formats can be used.


=== Section Based Events ===
In this example, ''two'' calendar events are created using the same page. The '''== event ==''' can be used to create these mulitple events per page. However, you can still create new page events by clicking ''Add Event''. You can force all new events into one page by using the ''usesectionevent'' parameter.

In this example, '''Picnic''' and '''Party''' will show up on the same day.
 <nowiki>
==Picnic==
Bring food!
==Party==
Bring drinks
</nowiki>


* '''The body requires some entry or the event will NOT save...'''

* If you're forcing users to reuse pages '''(usesectionevents)''', the addevent defaults to a page simular to the discussion (+) page. Ensure users use the Subject textbox and enter something in the body for the event to save. Using this method eliminates the <nowiki>==event==</nowiki> manual entry. However, you can omit the subject and still manually add section events in the body.

* use the '''disablesectionevents''' preference to diable the calendar from using sections as events.

=== Repeating Events ===
Repeating events (''if enabled'') allow an easy way to add the same event over multiple days. The below example will create 5 repeating Vacation events in the calendar. You MUST enable the functionality by adding '''enablerepeatevents''' to your parameter tag or config page. Enabling repeating events causes the calendar to look back 15 days into the previous month for any carry over events. If by chance you have a repeating event prior to the 15th, it will not carry over to the next month. 
 5# Vacation

=== Recurrence Event ===
Recurrence type events are traditional repeating yearly events like holidays, birthdays, etc. To create a recurrence event, choose 'add event' and add the following trigger syntax:
 ##My Birthday

This will convert the event into an vCalendar RRULE event and store it into the 'page/name/recurrence' wiki page. This is also where any ical recurrence events are stored.

=== Template Events ===
The template button (if enabled) allows users to add a bunch of events into one page. Only one template is created per month/year. This can be used along with all other event types.

The day and the event '''must''' be seperated by an '#' as shown in the example. You can also create duplicated days. The days do not have to be in order

 <nowiki>
1# Vacation
2# Holiday
7# Election Day
7# Office Closed
31# Half Day
19# Appointment
20-25# Hiking Trip <-- multiple day event
</nowiki>

=== Colors and Formatting ===
# The calendar supports most of the basic MediaWiki text/font properties including the 'ticks' for italic and bold.
#* <nowiki>'''<font color=red>vacation</font>'''</nowiki>
#* <span style="color:red;background:yellow">Vacation time!!</span> --> <nowiki><span style="color:red;background:yellow">Vacation time!!</span></nowiki>
# Setup the ''event style''' page by adding as many 'styles' as you wish. These styles are based on keyword matches, so be wary of what words you choose... The styles follow standard html/css style properties.
#* '''syntax:''' keyword:: style1; style2; etc
#** myStyle:: color:green; text-decoration:line-through; --> <s><span style="color:green">Whatever</span></s>
#** birthday:: color:red; font-style: bold; --> '''<font color=red>My Birthday</font>'''
#** sick:: color: green;background-color: yellow --> <span style="color:green;background-color:yellow">Out Sick today</span> <br/>
#** vacation:: color: red; font-style: italic --> ''<font color=red>Vacation to Florida!</font>''<br/>


I'm not sure how far and how many variation of the css and/or Wiki formatting will go, but I've tested a good portion of the standard text properties. (<nowiki><div></nowiki> is giving me an issue at this time though... but <nowiki><span></nowiki> works just fine!)


* you can default all events to your style preference by using the '''style="..."''' preference. However, individual keyword event styles override the global default.

=== Time Tracker ===
You can keep simple time tracking of events by formatting the event as below. This will track any dynamically created event in a simple table below the calendar in full mode only. The event is triggered by prefixing (2) colons followed by the event then (1) colon or (1) dash followed by a numeric value to add. 
 ::Vacation: 8 or ::Vacation -8
 ::Team Project 1 - 3
 ::Sick : 4

Note that events created using the 'add event' link only track time for that month. If you want to track a years total, you need to enable and use month templates ('''usetemplates''')

== vCalendar (iCal) Support ==
The calendar supports the basic importing of vCalendar formatted files. The import utility is enabled by adding '''ical''' or '''ical=overwrite''' to your parameter string or ''config'' file settings.<br>
<br>
The calendar accepts the following vCalendar formats
:DTSTART
:DTEND
:SUMMARY
:DESCRIPTION (not with RRULEs though)
:RRULE 

The RRULE evaluates basic calendar event logic only... nothing complex like "every 3rd Monday of every-other month". It does handle typical repeats like Thanksgiving, Mothers Day, etc that required logic like "the 4th Thursday of November" or "the last Monday of March" kinda logic. Basically, it should capture most repeating events like birthdays and holidays.


The RRULE (repeating) events are stored in a subpage called '''recurrence'''. Basically, in the following format ''page/calendarname/recurrence''. You can manually edit or delete these as needed. If you use the ''ical=overwrite'' option, it deletes the data before writing in the new ical data. 


Imported single day events, without the RRULE, are created in the calendar as normal pages in the -Event 0 page for the respective day.

== Internationalization (i18n)==
The calendar months and weekday names will display in any MediaWiki language selected in the user preferences. However, the custom buttons and other calendar specific information has only been converted to French (fr), Spanish (es), German (de), Hungarian (hu),Polish (nl) and Finnish(fi). 


If any other languages are required, new messages structures will have to be created in the ''calendar.i18n.php'' file as needed by the user. It's not hard really, just copy an existing message structure in that file and update the required translations. It would take all of 15 minutes to add additional languages. The calendar logic is coded as such that any new message structures added will auto-load and be available right away! If you '''post''' the newly created language to the '''google issue tracker''', I'll add it into the language file below.


I update the trunk code everytime someone posts a language translation. Please check here for the latest [http://code.google.com/p/mw-calendar/source/browse/trunk/calendar.i18n.php calendar.i18n.php] file.

<br>

== Quick Sheet ==
<table border=1 cellpadding=5>
<th>method</th>
<th>example</th>
<th>results</th>
<tr>
  <td>repeating event (add event method)</td>
  <td>5#Vacation</td>
  <td>Creates 5 repeating event days</td>
</tr>
<tr>
  <td>repeating event (template method)</td>
  <td>5-10#Vacation</td>
  <td>Creates event days starting on the 5th continuing until the 10th</td>
</tr>
<tr>
  <td>create a reoccurring yearly event (add event)</td>
  <td>##My Birthday</td>
  <td>Creates this event on this day every year</td>
</tr>
<tr>
  <td>create multiple events for one day using one page</td>
  <td>== event 1 ==<br>== event 2 ==</td>
  <td>Creates two events on one page</td>
</tr>
</table>

== Tips/Tricks! ==
* Create a new calendar event and use '''<nowiki>#REDIRECT[[page]]</nowiki>''' to forward the new event to a new non-calendar page!
* Click "add event", create the event title on line one and use '''<nowiki>{{:page}}</nowiki>''' to copy a remote page into a calendar event body!
* have alot of calendar preferences...? Use the ''<nowiki><calendar name=SomeName useconfigpage /></nowiki>'' option and move all your preference to the config page instead!

== Installation ==
The following are details of the administrator installation of this calendar extension. If you dont have any custom Namespaces, then 100 and 101 are fine, if you do have existing custom Namespaces, just bump the numbers up accordingly. See [http://www.mediawiki.org/wiki/Help:Namespaces Help:Namespaces] for more information. The $wgNamespacesWithSubpages values must match the values assigned to the $wgExtraNamespaces.


The default date format is '''M-D-YYYY''', please change this if needed with the '''$wgCalendarDateFormat''' override below before you go live with your calendar.


 '''Recommended Folder Path:''' /extensions/Calendar
'''Localsettings.php:'''<br/>
<br/>
''Simple'':
 require_once("$IP/extensions/Calendar/Calendar.php");<br/>
 
''Recommended'':
 require_once("$IP/extensions/Calendar/Calendar.php");<br/>
 
 // Puts events into their own namesspace/group (not included in 'main' searches... etc)
 $wgExtraNamespaces[100] = "Calendars";
 $wgExtraNamespaces[101] = "Calendars_talk";
 //''Note: 'Calendars' is an example, please feel free to use whatever name you wish''
 
 // Puts the events into Subpages (allows a quick link back to primary calendar)
 $wgNamespacesWithSubpages[100] = true;
 $wgNamespacesWithSubpages[101] = true;
 
 $wgCalendarForceNamespace='Calendars'

The additional namespaces move all the events outside the "main" group... should clean the mess up some. If you have custom namespaces installed already, make sure you bump up the [100][101] values up accordingly.

==== Optional LocalSetting.php Settings ====
{| border=1 width=75%
!Override
!Description
!Version
|-
| nowrap | $wgRestrictCalendarTo = 'sysop';
| You can put the whole wiki site into ''Calendar Lockdown'' with the following entry. The value can be any defined group in your wiki site.
| &nbsp;
|-
| nowrap | $wgCalendarURLPath="/w/extensions/Calendar";
| if for any reason, the calendar CSS file path is invalid, please set the calendar root URL manually
| &nbsp;
|-
| nowrap | $wgCalendarDisableRedirects=true
| disables calendar event redirects globally
| &nbsp;
|-
| nowrap | $wgCalendarForceNamespace='Calendar'
| only allow calendars to be created in the required namespace
| &nbsp;
|-
| nowrap | $wgCalendarDateFormat=YYYYMMDD
| use YYYY, MM, DD, M, D, SM, LM in any format <br>''(SM D, YYYY --> Jul 1, 2009)'' <br>(''YYYYMMDD --> 20090701)''<br>'''Note:''' Previous events will not display if you change date formats.
| 3.8
|}

== Date Conversion Tool (v3.8.1) ==
'''Please <u>'''TEST'''</u> using a test calendar before using on a LIVE calendar.'''   


* Used in conjunction with global '''$wgCalendarDateFormat=YYYYMMDD'''


This tool allows admins to convert all wiki calendar pages from MM-DD-YYY to a custom user defined format. This only converts from the legacy date format to the custom format so technically can only be used to convert one time. The new format can be used in any manner or order.

This tool finds the original calendar events and moves the to new pages using the newer date format.


 require_once( "$IP/extensions/calendar/dateConverter.php" );

'''Parameters:'''
*'''newformat''': (YYYY MM DD M D SM LM) SM=short month, LM=long month (default: YYYYMMDD)
*'''pagename''': wiki pagename including namespace as needed
*'''calname''': name of the calendar (default: Public)
*'''redirect''': add redirect link to old original event pages. (default: no redirect links, old page is removed)


The following examples do not convert the wiki title/pages; its more of a "test" run...
 <dateConverter pagename='Calendars:TeamPage' calname='Team Calendar' newFormat='YYYYMMDD' /> -- 20090805
 <dateConverter pagename='Calendars:TeamPage' calname='Team Calendar' newFormat='SD D, YYYY' /> -- Jul 1, 2009

Once you test the script, you MUST add ''''go'''' to the tag to acutally convert the events to the new format
 <dateConverter pagename='Calendars:TeamPage' calname='Team Calendar' newFormat='YYYYMMDD' '''go''' />

== Troublehooting ==
* If you have an issue with the calendar display, try setting '''<code>$wgUseTidy = false;</code>''' in LocalSettings.php.
