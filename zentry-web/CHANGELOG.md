## [1.6.0] - 2020-09-18
### Added
- Sessions
    - Notes
    - Widget
        - Participants events
### Removed
- Sessions
    - Recorded
        - Now only raw session is used
### Fixed
- NgPlural
- Sessions
    - Widget
        - Loader wasn't removed on cancel confirmation
    - View cannot be accessed if transcript failed

## [1.5.3] - 2020-09-14
### Fixed
- Recorded
    - Share
        - Participant
            - Unability to change remove participants

## [1.5.2] - 2020-08-17
### Fixed
- end session via event
- multiple clips participants

## [1.5.1] - 2020-08-04
### Fixed
- trackpad.component.ts, POI unlocked the end session button even if the recording is still going

## [1.5.0] - 2020-08-04
### Added

- trackpad.component.ts, ability to make POI during recording of clip and make more steps of backtrack

### Changed

- trackpad.component.html, composition
- timer.component.ts, from in ms instead of amount of ms

### Removed

- .gitlab-ci.yml, zentry references

### Fixed
- docker network

- coloring and base styles
- profile.component.html, phone code responsive (#23)
- profile.component.html, phone code, number, first name, last name, password min max length (#25, #34)

- session.service.ts, issue with old active session from db (POIs was loaded from old session to the list)

- clip.component.html, scroll always visible (#43)
- clip.component.html, trash tooltip (#44)
- clip.component.html, clips ordering (#50)
- participant.component.html, select value instead of empty select on create (#153)
- widget.component.html, fixed tooltip placement for mute audio (#143)

- recording, view.component.html, show error modal if the location tracking is not allowed (#55)
- recording, view.component.html, tags, remove animation (#58, #83)
- recording, view.component.html, list.component.html long name cut (#59, #61)
- recording, view.component.html, tags delete icon align (#86)
- recording, clip.component.html, more clear message on empty clips list (#62)
- recording, view.component.html, name of the downloaded file is now the recording name (#68)
- recording, view.component.html, tags delete icon align (#86)

- header.component.html, logout loader (#82)
- registration.component.html, validation of the first step (#111)
- registration.component.html , first name, last name, password min max length (#25)

## [1.4.2] - 2020-07-28
### Fixed
- Participants
    - Student term for Zentry
    
## [1.4.1] - 2020-07-22
### Fixed
- Sessions
    - Recorded
        - View
            - Transcript
                - Order was incorrect

## [1.4.0] - 2020-07-22
### Added
- Navbar
    - Folded menus
- Users
    - Participants
- Sessions
    - Widget
        - Participants
            - POI
                - Participants
    - Recorded
        - Participants
        - POI
            - Participants
        - Transcript
- Sorting

### Refactored
- Sessions
    - Recorded
        - View
            - Structure
- Widget 
    - Structure
    
### Fixed
- Sessions
    - Recorded
        - View
            - Tags onBlur event
- Widget 
    - Structure
    
## [1.3.1] - 2020-07-07
### Fixed
- Shares
    - Readonly if authorized user is not owner of the recording

## [1.3.0] - 2020-07-07
### Added
- Authentication
    - Google
- Shares

### Fixed
- UI issues

## [1.2.1] - 2020-06-30
### Fixed
- Zentry
    - Styling

## [1.2.0] - 2020-06-30
### Added
- Users
    - Storages
- Responsive
    - Phones
    - Tablets

### Fixed
- Bugs list as per first regression

## [1.1.1] - 2020-06-09
### Fixed
- Audio Constraints and Flickering

## [1.1.0] - 2020-06-07
### Added
- Sessions
    - Ability to upload stream in chunks

### Optimized
- Sessions
    - Widget
        - Performance

### Fixed
- Basic Review of v1.0.0

## [1.0.0] - 2020-05-18
### Added
- Authentication
    - token
- Users
    - Profile
- Sessions
    - Full

## [0.1.0] - 2020-04-28
### Added
- Authentication
- Sockets
- Dashboard
- Users
    - Devices
- Sessions
    - Recorded
    - Widget
